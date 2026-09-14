#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$options = parseOptions(array_slice($argv, 1));
$temporary = sys_get_temp_dir() . '/larena-docara-release-' . bin2hex(random_bytes(6));

try {
    $lock = readJson($root . '/composer.lock');
    $packages = array_merge(
        is_array($lock['packages'] ?? null) ? $lock['packages'] : [],
        is_array($lock['packages-dev'] ?? null) ? $lock['packages-dev'] : [],
    );
    $docara = array_values(array_filter(
        $packages,
        static fn (mixed $package): bool => is_array($package)
            && ($package['name'] ?? null) === 'simai/docara',
    ));
    if (count($docara) !== 1) {
        throw new RuntimeException('composer.lock must contain exactly one simai/docara package.');
    }

    $installedVersion = ltrim((string) ($docara[0]['version'] ?? ''), 'v');
    $installedRevision = (string) ($docara[0]['source']['reference'] ?? '');
    if (preg_match('/^\d+\.\d+\.\d+$/', $installedVersion) !== 1
        || preg_match('/^[0-9a-f]{40}$/', $installedRevision) !== 1
    ) {
        throw new RuntimeException('Installed Docara must resolve to an exact stable version and source revision.');
    }

    $repository = (string) ($options['repository']
        ?? getenv('DOCARA_UPSTREAM_REPOSITORY')
        ?: ($docara[0]['source']['url'] ?? ''));
    if ($repository === '') {
        throw new RuntimeException('Docara upstream repository is not configured.');
    }

    mkdir($temporary, 0700, true);
    git($temporary, ['init', '--bare', '--quiet']);
    git($temporary, [
        'fetch', '--quiet', '--force', '--filter=blob:none', '--tags', $repository,
        '+refs/heads/main:refs/remotes/origin/main',
    ]);

    $tags = gitLines($temporary, ['tag', '--list', 'v*', '--sort=-v:refname']);
    $latestTag = null;
    foreach ($tags as $tag) {
        if (preg_match('/^v\d+\.\d+\.\d+$/', $tag) === 1) {
            $latestTag = $tag;
            break;
        }
    }
    if ($latestTag === null) {
        throw new RuntimeException('Docara upstream has no stable semantic-version tag.');
    }

    $latestVersion = substr($latestTag, 1);
    $latestRevision = git($temporary, ['rev-list', '-n', '1', $latestTag]);
    $mainRevision = git($temporary, ['rev-parse', 'refs/remotes/origin/main']);
    $relevantCommits = gitLines($temporary, [
        'log', '--format=%H', $latestTag . '..refs/remotes/origin/main', '--', '.',
        ':(exclude).github/release-request.json',
    ]);

    $errors = [];
    if ($installedVersion !== $latestVersion) {
        $errors[] = 'installed_docara_is_not_latest_stable';
    }
    if ($installedRevision !== $latestRevision) {
        $errors[] = 'installed_docara_revision_does_not_match_stable_tag';
    }
    if ($relevantCommits !== []) {
        $errors[] = 'unreleased_docara_changes_on_main';
    }

    $result = [
        'schema' => 'larena.docara_release_status.v1',
        'status' => $errors === [] ? 'current' : 'stale',
        'installed' => [
            'version' => $installedVersion,
            'revision' => $installedRevision,
        ],
        'upstream' => [
            'repository' => $repository,
            'latest_stable_tag' => $latestTag,
            'latest_stable_revision' => $latestRevision,
            'main_revision' => $mainRevision,
            'relevant_commits_after_stable' => $relevantCommits,
        ],
        'errors' => $errors,
    ];
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    exit($errors === [] || !isset($options['require-current']) ? 0 : 1);
} catch (Throwable $exception) {
    echo json_encode([
        'schema' => 'larena.docara_release_status.v1',
        'status' => 'failed',
        'error' => $exception->getMessage(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    exit(1);
} finally {
    removeTree($temporary);
}

/** @param list<string> $arguments
 *  @return array<string,string|bool>
 */
function parseOptions(array $arguments): array
{
    $options = [];
    foreach ($arguments as $argument) {
        if (!str_starts_with($argument, '--')) {
            throw new InvalidArgumentException("Unexpected argument: {$argument}");
        }
        $pair = explode('=', substr($argument, 2), 2);
        $options[$pair[0]] = $pair[1] ?? true;
    }

    return $options;
}

/** @return array<string,mixed> */
function readJson(string $path): array
{
    if (!is_file($path)) {
        throw new RuntimeException("Missing JSON file: {$path}");
    }
    $value = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($value)) {
        throw new RuntimeException("JSON root must be an object: {$path}");
    }

    return $value;
}

/** @param list<string> $arguments */
function git(string $repository, array $arguments): string
{
    [$exit, $stdout, $stderr] = process(array_merge(['git'], $arguments), $repository);
    if ($exit !== 0) {
        throw new RuntimeException('Git command failed: ' . trim($stderr . "\n" . $stdout));
    }

    return trim($stdout);
}

/** @param list<string> $arguments
 *  @return list<string>
 */
function gitLines(string $repository, array $arguments): array
{
    $output = git($repository, $arguments);

    return $output === '' ? [] : (preg_split('/\R/', $output) ?: []);
}

/** @param list<string> $command
 *  @return array{0:int,1:string,2:string}
 */
function process(array $command, string $cwd): array
{
    $pipes = [];
    $process = proc_open($command, [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes, $cwd);
    if (!is_resource($process)) {
        throw new RuntimeException('Unable to start process.');
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]) ?: '';
    $stderr = stream_get_contents($pipes[2]) ?: '';
    fclose($pipes[1]);
    fclose($pipes[2]);

    return [proc_close($process), $stdout, $stderr];
}

function removeTree(string $path): void
{
    if (!is_dir($path) || is_link($path)) {
        return;
    }
    $items = scandir($path);
    if (!is_array($items)) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $target = $path . '/' . $item;
        if (is_dir($target) && !is_link($target)) {
            removeTree($target);
        } else {
            unlink($target);
        }
    }
    rmdir($path);
}
