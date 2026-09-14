#!/usr/bin/env php
<?php

declare(strict_types=1);

$project = sys_get_temp_dir() . '/larena-doc-impact-' . bin2hex(random_bytes(5));
$repo = $project . '/source-core';

try {
    mkdir($project . '/scripts', 0775, true);
    mkdir($project . '/contracts', 0775, true);
    mkdir($project . '/content/ru/packages', 0775, true);
    copy(dirname(__DIR__) . '/scripts/documentation-impact.php', $project . '/scripts/documentation-impact.php');
    file_put_contents($project . '/content/ru/packages/core.md', "# Core\n\nVerified page.\n");

    mkdir($repo, 0775, true);
    run(['git', 'init', '-q'], $repo);
    run(['git', 'config', 'user.name', 'Larena Documentation Test'], $repo);
    run(['git', 'config', 'user.email', 'docs-test@example.invalid'], $repo);
    file_put_contents($repo . '/README.md', "first\n");
    file_put_contents($repo . '/composer.json', "{\"name\":\"larena/core\",\"require\":{\"php\":\"^8.3\"}}\n");
    run(['git', 'add', 'README.md', 'composer.json'], $repo);
    run(['git', 'commit', '-q', '-m', 'baseline'], $repo);
    $baseline = trim(run(['git', 'rev-parse', 'HEAD'], $repo)[1]);
    file_put_contents($repo . '/README.md', "second\n");
    run(['git', 'add', 'README.md'], $repo);
    run(['git', 'commit', '-q', '-m', 'public change'], $repo);
    $current = trim(run(['git', 'rev-parse', 'HEAD'], $repo)[1]);

    $map = [
        'schema' => 'larena.documentation_map.v1',
        'source_contract_file' => 'contracts/larena-documentation-source.json',
        'decision_file' => 'contracts/larena-documentation-decisions.json',
        'sources' => [[
            'id' => 'package.core',
            'label' => 'larena/core',
            'repository' => 'source-core',
            'verified_revision' => $baseline,
            'public_paths' => ['README.md', 'composer.json', 'src/Contracts/**'],
        ]],
        'pages' => [[
            'id' => 'packages.core',
            'path' => 'content/ru/packages/core.md',
            'route' => '/ru/packages/core/',
            'title' => 'larena/core',
            'audience' => 'developer',
            'outcome' => 'Use core.',
            'owner' => 'larena-doc',
            'sources' => [[
                'source' => 'package.core',
                'paths' => ['README.md', 'composer.json', 'src/Contracts/**'],
            ]],
            'docara_entity' => 'larena.package.core',
        ]],
    ];
    writeJson($project . '/contracts/larena-documentation-map.json', $map);
    writeJson($project . '/contracts/larena-documentation-decisions.json', [
        'schema' => 'larena.documentation_decisions.v1',
        'decisions' => [],
    ]);

    [$exit, $stdout] = run([PHP_BINARY, $project . '/scripts/documentation-impact.php', 'status'], $project, false);
    assertSame(0, $exit, 'status exit');
    $status = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame('decision_required', $status['status'], 'public change is stale');
    assertSame(['packages.core'], array_column($status['stale_pages'], 'id'), 'affected page');

    [$exit] = run([
        PHP_BINARY, $project . '/scripts/documentation-impact.php', 'accept',
        '--source=package.core', '--decision=documentation_updated', '--reason=Updated public Core guide.',
    ], $project, false);
    assertSame(1, $exit, 'updated decision without all pages fails');

    [$exit, $stdout] = run([
        PHP_BINARY, $project . '/scripts/documentation-impact.php', 'accept',
        '--source=package.core', '--decision=documentation_updated', '--reason=Updated public Core guide.',
        '--page=packages.core',
    ], $project, false);
    assertSame(0, $exit, 'complete decision passes');
    $accepted = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame('accepted', $accepted['status'], 'decision status');

    [$exit, $stdout] = run([PHP_BINARY, $project . '/scripts/documentation-impact.php', 'status'], $project, false);
    $status = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame(0, $exit, 'current status exit');
    assertSame('current', $status['status'], 'accepted revision is current');

    [$exit, $stdout] = run([
        PHP_BINARY, $project . '/scripts/documentation-impact.php', 'impact',
        '--changed-file=package.core:src/Internal/Cache.php',
    ], $project, false);
    $internal = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame('current', $internal['status'], 'internal-only path has no public impact');

    [$exit, $stdout] = run([
        PHP_BINARY, $project . '/scripts/documentation-impact.php', 'impact',
        '--changed-file=package.core:src/Contracts/NewContract.php',
    ], $project, false);
    $contract = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame('decision_required', $contract['status'], 'contract path has public impact');
    assertSame($current, $contract['sources'][0]['verified_revision'], 'accepted revision stored');

    $ledger = json_decode((string) file_get_contents($project . '/contracts/larena-documentation-decisions.json'), true, 512, JSON_THROW_ON_ERROR);
    assertSame('documentation_updated', $ledger['decisions'][0]['decision'], 'decision ledger');
    assertSame(['packages.core'], $ledger['decisions'][0]['updated_pages'], 'updated page hashes recorded');

    echo json_encode([
        'schema' => 'larena.documentation_impact_test.v1',
        'status' => 'passed',
        'assertions' => 12,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
} finally {
    removeTree($project);
}
/**
 * @param list<string> $command
 * @return array{0:int,1:string,2:string}
 */
function run(array $command, string $cwd, bool $throw = true): array
{
    $pipes = [];
    $process = proc_open($command, [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes, $cwd);
    if (!is_resource($process)) {
        throw new RuntimeException('Unable to start test process.');
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]) ?: '';
    $stderr = stream_get_contents($pipes[2]) ?: '';
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exit = proc_close($process);
    if ($throw && $exit !== 0) {
        throw new RuntimeException(implode(' ', $command) . ': ' . trim($stderr . "\n" . $stdout));
    }

    return [$exit, $stdout, $stderr];
}

function writeJson(string $path, array $value): void
{
    file_put_contents($path, json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n");
}

function assertSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function removeTree(string $path): void
{
    if (!is_dir($path)) {
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
