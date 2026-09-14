#!/usr/bin/env php
<?php

declare(strict_types=1);

const MAP_SCHEMA = 'larena.documentation_map.v1';
const DECISIONS_SCHEMA = 'larena.documentation_decisions.v1';
const REPORT_SCHEMA = 'larena.documentation_impact_report.v1';

$root = dirname(__DIR__);
$args = parseArguments(array_slice($argv, 1));
$command = $args['positionals'][0] ?? 'status';

try {
    $mapPath = $root . '/contracts/larena-documentation-map.json';
    $map = readJsonFile($mapPath);
    assertMap($root, $map);

    $report = buildImpactReport($root, $map, $args['options']['changed-file'] ?? []);

    if ($command === 'status' || $command === 'impact') {
        output($report);
        exit(0);
    }

    if ($command === 'source-contract') {
        $contract = buildSourceContract($root, $map);
        $target = $root . '/' . $map['source_contract_file'];
        if (isset($args['options']['write'])) {
            if ($report['summary']['stale_sources'] > 0 || $report['errors'] !== []) {
                throw new RuntimeException('Cannot generate the Docara source contract while documentation decisions are pending.');
            }
            writeJsonAtomic($target, $contract);
            output([
                'schema' => 'larena.documentation_source_projection_result.v1',
                'status' => 'written',
                'file' => relativePath($root, $target),
                'entities' => count($contract['entities']),
                'sha256' => hash_file('sha256', $target),
            ]);
            exit(0);
        }

        $comparison = compareJsonFile($target, $contract);
        output([
            'schema' => 'larena.documentation_source_projection_result.v1',
            'status' => $comparison['current'] ? 'current' : 'stale',
            'file' => relativePath($root, $target),
            'entities' => count($contract['entities']),
            'expected_sha256' => $comparison['expected_sha256'],
            'actual_sha256' => $comparison['actual_sha256'],
        ]);
        exit($comparison['current'] ? 0 : 1);
    }

    if ($command === 'accept') {
        $result = acceptDecision($root, $map, $report, $args['options']);
        output($result);
        exit(0);
    }

    if ($command === 'check') {
        $contract = buildSourceContract($root, $map);
        $projection = compareJsonFile($root . '/' . $map['source_contract_file'], $contract);
        $docara = docaraStatus($root, isset($args['options']['require-docara']));
        $errors = $report['errors'];
        if ($report['summary']['stale_sources'] > 0) {
            $errors[] = 'documentation_decision_required';
        }
        if (!$projection['current']) {
            $errors[] = 'docara_source_projection_stale';
        }
        if (($docara['status'] ?? '') === 'failed') {
            $errors[] = 'docara_documentation_tracking_failed';
        }
        if (($docara['status'] ?? '') === 'skipped' && isset($args['options']['require-docara'])) {
            $errors[] = 'docara_documentation_tracking_required';
        }
        $result = [
            'schema' => 'larena.documentation_sync_check.v1',
            'status' => $errors === [] ? 'passed' : 'failed',
            'impact' => $report,
            'source_projection' => [
                'status' => $projection['current'] ? 'current' : 'stale',
                'expected_sha256' => $projection['expected_sha256'],
                'actual_sha256' => $projection['actual_sha256'],
            ],
            'docara' => $docara,
            'errors' => array_values(array_unique($errors)),
        ];
        output($result);
        exit($errors === [] ? 0 : 1);
    }

    throw new InvalidArgumentException("Unknown command: {$command}");
} catch (Throwable $exception) {
    output([
        'schema' => 'larena.documentation_sync_error.v1',
        'status' => 'failed',
        'error' => $exception->getMessage(),
    ]);
    exit(1);
}

/**
 * @param list<string> $arguments
 * @return array{positionals:list<string>,options:array<string,mixed>}
 */
function parseArguments(array $arguments): array
{
    $positionals = [];
    $options = [];
    foreach ($arguments as $argument) {
        if (!str_starts_with($argument, '--')) {
            $positionals[] = $argument;
            continue;
        }
        $pair = explode('=', substr($argument, 2), 2);
        $key = $pair[0];
        $value = $pair[1] ?? true;
        if (in_array($key, ['changed-file', 'page'], true)) {
            $options[$key] ??= [];
            $options[$key][] = $value;
        } else {
            $options[$key] = $value;
        }
    }

    return ['positionals' => $positionals, 'options' => $options];
}

/** @return array<string,mixed> */
function readJsonFile(string $path): array
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

/** @param array<string,mixed> $map */
function assertMap(string $root, array $map): void
{
    if (($map['schema'] ?? null) !== MAP_SCHEMA) {
        throw new RuntimeException('Unsupported documentation map schema.');
    }
    if (!is_array($map['sources'] ?? null) || !is_array($map['pages'] ?? null)) {
        throw new RuntimeException('Documentation map requires sources and pages.');
    }
    $sourceIds = [];
    foreach ($map['sources'] as $source) {
        if (!is_array($source) || !is_string($source['id'] ?? null) || isset($sourceIds[$source['id']])) {
            throw new RuntimeException('Documentation map contains an invalid or duplicate source.');
        }
        $sourceIds[$source['id']] = true;
        foreach (['repository', 'verified_revision', 'public_paths'] as $field) {
            if (!isset($source[$field])) {
                throw new RuntimeException("Source {$source['id']} is missing {$field}.");
            }
        }
    }
    $pageIds = [];
    $entities = [];
    foreach ($map['pages'] as $page) {
        if (!is_array($page) || !is_string($page['id'] ?? null) || isset($pageIds[$page['id']])) {
            throw new RuntimeException('Documentation map contains an invalid or duplicate page.');
        }
        $pageIds[$page['id']] = true;
        $path = $root . '/' . ($page['path'] ?? '');
        if (!is_file($path)) {
            throw new RuntimeException("Mapped page is missing: {$page['path']}");
        }
        foreach ($page['sources'] ?? [] as $rule) {
            if (!is_array($rule) || !isset($sourceIds[$rule['source'] ?? '']) || !is_array($rule['paths'] ?? null)) {
                throw new RuntimeException("Page {$page['id']} contains an invalid source rule.");
            }
        }
        if (isset($page['docara_entity'])) {
            if (isset($entities[$page['docara_entity']])) {
                throw new RuntimeException("Duplicate Docara entity: {$page['docara_entity']}");
            }
            $entities[$page['docara_entity']] = true;
        }
    }
}

/**
 * @param array<string,mixed> $map
 * @param list<mixed> $virtualArguments
 * @return array<string,mixed>
 */
function buildImpactReport(string $root, array $map, array $virtualArguments): array
{
    $virtual = [];
    foreach ($virtualArguments as $argument) {
        if (!is_string($argument) || !str_contains($argument, ':')) {
            throw new InvalidArgumentException('--changed-file must use source:path.');
        }
        [$source, $path] = explode(':', $argument, 2);
        $virtual[$source][] = normalizePath($path);
    }

    $sources = [];
    $errors = [];
    $stalePages = [];
    foreach ($map['sources'] as $source) {
        $id = $source['id'];
        $repository = resolveRepository($root, $source['repository']);
        if (!is_dir($repository . '/.git') && !is_file($repository . '/.git')) {
            $errors[] = "source_repository_missing:{$id}";
            $sources[] = ['id' => $id, 'status' => 'missing', 'repository' => $source['repository']];
            continue;
        }
        $current = git($repository, ['rev-parse', 'HEAD']);
        $verified = $source['verified_revision'];
        $committed = $current === $verified ? [] : gitLines($repository, ['diff', '--no-renames', '--name-only', $verified . '..' . $current, '--']);
        $dirty = dirtyPaths($repository);
        $changed = array_values(array_unique(array_merge($committed, $dirty, $virtual[$id] ?? [])));
        sort($changed, SORT_STRING);
        $publicChanged = array_values(array_filter(
            $changed,
            static fn (string $path): bool => matchesAny($path, $source['public_paths'])
        ));
        $impacted = [];
        foreach ($map['pages'] as $page) {
            foreach ($page['sources'] as $rule) {
                if ($rule['source'] !== $id) {
                    continue;
                }
                if (array_filter($publicChanged, static fn (string $path): bool => matchesAny($path, $rule['paths'])) !== []) {
                    $impacted[] = $page['id'];
                    $stalePages[$page['id']] = true;
                    break;
                }
            }
        }
        sort($impacted, SORT_STRING);
        $sources[] = [
            'id' => $id,
            'label' => $source['label'],
            'status' => $publicChanged === [] ? 'current' : 'decision_required',
            'verified_revision' => $verified,
            'current_revision' => $current,
            'worktree_public_changes' => array_values(array_intersect($dirty, $publicChanged)),
            'changed_paths' => $changed,
            'public_changed_paths' => $publicChanged,
            'changed_paths_sha256' => hash('sha256', implode("\n", $publicChanged)),
            'impacted_pages' => $impacted,
            'irrelevant_changes' => array_values(array_diff($changed, $publicChanged)),
            'virtual_change' => isset($virtual[$id]),
        ];
    }

    $stalePageItems = [];
    foreach ($map['pages'] as $page) {
        if (isset($stalePages[$page['id']])) {
            $stalePageItems[] = [
                'id' => $page['id'],
                'path' => $page['path'],
                'route' => $page['route'],
                'title' => $page['title'],
                'audience' => $page['audience'],
                'outcome' => $page['outcome'],
                'owner' => $page['owner'],
            ];
        }
    }

    $staleSources = count(array_filter($sources, static fn (array $source): bool => ($source['status'] ?? '') === 'decision_required'));

    return [
        'schema' => REPORT_SCHEMA,
        'status' => $errors === [] && $staleSources === 0 ? 'current' : ($errors === [] ? 'decision_required' : 'failed'),
        'summary' => [
            'sources' => count($sources),
            'pages' => count($map['pages']),
            'stale_sources' => $staleSources,
            'stale_pages' => count($stalePageItems),
        ],
        'sources' => $sources,
        'stale_pages' => $stalePageItems,
        'errors' => $errors,
    ];
}

/**
 * @param array<string,mixed> $map
 * @return array<string,mixed>
 */
function buildSourceContract(string $root, array $map): array
{
    $sourceIndex = [];
    foreach ($map['sources'] as $source) {
        $source['absolute_repository'] = resolveRepository($root, $source['repository']);
        $source['current_revision'] = git($source['absolute_repository'], ['rev-parse', 'HEAD']);
        $sourceIndex[$source['id']] = $source;
    }

    $entities = [];
    foreach ($map['pages'] as $page) {
        if (!isset($page['docara_entity'])) {
            continue;
        }
        $parts = explode('.', $page['docara_entity']);
        $package = end($parts);
        $contractParts = [];
        $provenance = [];
        foreach ($page['sources'] as $rule) {
            $source = $sourceIndex[$rule['source']];
            $contractParts[$rule['source']] = digestRepositoryFiles($source['absolute_repository'], $rule['paths']);
            foreach ($rule['paths'] as $pattern) {
                $provenance[] = $rule['source'] . '@' . $source['current_revision'] . ':' . $pattern;
            }
        }
        ksort($contractParts, SORT_STRING);
        sort($provenance, SORT_STRING);
        $packageSource = $sourceIndex['package.' . $package] ?? null;
        $dependencies = [];
        if (is_array($packageSource)) {
            $composerPath = $packageSource['absolute_repository'] . '/composer.json';
            if (is_file($composerPath)) {
                $composer = readJsonFile($composerPath);
                $dependencies = is_array($composer['require'] ?? null) ? $composer['require'] : [];
                ksort($dependencies, SORT_STRING);
            }
        }
        $requirements = [];
        $specsSource = $sourceIndex['specs'] ?? null;
        if (is_array($specsSource)) {
            $featuresPath = $specsSource['absolute_repository'] . '/specs/packages/' . $package . '/features.json';
            if (is_file($featuresPath)) {
                $features = readJsonFile($featuresPath);
                foreach ($features['features'] ?? [] as $feature) {
                    if (!is_array($feature) || !is_string($feature['id'] ?? null)) {
                        continue;
                    }
                    $requirements[] = [
                        'id' => $feature['id'],
                        'status' => $feature['status'] ?? null,
                        'path' => $feature['path'] ?? null,
                    ];
                }
                usort($requirements, static fn (array $a, array $b): int => strcmp($a['id'], $b['id']));
            }
        }
        $entities[] = [
            'key' => $page['docara_entity'],
            'kind' => 'package',
            'title' => $page['title'],
            'public_contract' => [
                'package' => 'larena/' . $package,
                'dependencies' => $dependencies,
                'spec_requirements' => $requirements,
                'contract_parts' => $contractParts,
            ],
            'example_cases' => [],
            'provenance' => $provenance,
        ];
    }
    usort($entities, static fn (array $a, array $b): int => strcmp($a['key'], $b['key']));
    $revisionParts = [];
    foreach ($sourceIndex as $id => $source) {
        $revisionParts[$id] = $source['current_revision'];
    }
    ksort($revisionParts, SORT_STRING);

    return [
        'schema' => 'docara.documentation_source.v1',
        'id' => 'larena',
        'provider' => 'contract_json',
        'revision' => hash('sha256', canonicalJson($revisionParts)),
        'entities' => $entities,
    ];
}

/**
 * @param array<string,mixed> $map
 * @param array<string,mixed> $report
 * @param array<string,mixed> $options
 * @return array<string,mixed>
 */
function acceptDecision(string $root, array $map, array $report, array $options): array
{
    $sourceId = is_string($options['source'] ?? null) ? $options['source'] : '';
    $decision = is_string($options['decision'] ?? null) ? $options['decision'] : '';
    $reason = trim(is_string($options['reason'] ?? null) ? $options['reason'] : '');
    $pages = array_values(array_unique(array_filter(
        is_array($options['page'] ?? null) ? $options['page'] : [],
        'is_string'
    )));
    sort($pages, SORT_STRING);

    if (!in_array($decision, ['documentation_updated', 'documentation_not_affected'], true)) {
        throw new InvalidArgumentException('--decision must be documentation_updated or documentation_not_affected.');
    }
    if (mb_strlen($reason) < 12) {
        throw new InvalidArgumentException('--reason must explain the decision in at least 12 characters.');
    }
    $sourceReport = null;
    foreach ($report['sources'] as $candidate) {
        if (($candidate['id'] ?? null) === $sourceId) {
            $sourceReport = $candidate;
            break;
        }
    }
    if (!is_array($sourceReport)) {
        throw new InvalidArgumentException("Unknown source: {$sourceId}");
    }
    if (($sourceReport['status'] ?? null) !== 'decision_required') {
        throw new RuntimeException("Source {$sourceId} has no pending public documentation impact.");
    }
    if (($sourceReport['worktree_public_changes'] ?? []) !== []) {
        throw new RuntimeException("Source {$sourceId} has uncommitted public-contract changes; accept after committing the source revision.");
    }
    $impacted = $sourceReport['impacted_pages'];
    sort($impacted, SORT_STRING);
    if ($decision === 'documentation_updated' && $pages !== $impacted) {
        throw new RuntimeException('documentation_updated requires one --page for every impacted page: ' . implode(', ', $impacted));
    }
    if ($decision === 'documentation_not_affected' && $pages !== []) {
        throw new RuntimeException('documentation_not_affected does not accept --page; the impacted candidates remain recorded in the decision.');
    }

    $pageHashes = [];
    if ($decision === 'documentation_updated') {
        foreach ($map['pages'] as $page) {
            if (in_array($page['id'], $pages, true)) {
                $pageHashes[$page['id']] = hash_file('sha256', $root . '/' . $page['path']);
            }
        }
        ksort($pageHashes, SORT_STRING);
    }

    $record = [
        'source' => $sourceId,
        'from_revision' => $sourceReport['verified_revision'],
        'to_revision' => $sourceReport['current_revision'],
        'changed_paths' => $sourceReport['public_changed_paths'],
        'changed_paths_sha256' => $sourceReport['changed_paths_sha256'],
        'impacted_pages' => $impacted,
        'decision' => $decision,
        'reason' => $reason,
        'updated_pages' => $pages,
        'page_sha256' => $pageHashes,
        'accepted_at' => gmdate('c'),
    ];
    $record['id'] = hash('sha256', canonicalJson($record));

    $decisionPath = $root . '/' . $map['decision_file'];
    $ledger = readJsonFile($decisionPath);
    if (($ledger['schema'] ?? null) !== DECISIONS_SCHEMA || !is_array($ledger['decisions'] ?? null)) {
        throw new RuntimeException('Invalid documentation decision ledger.');
    }
    $ledger['decisions'][] = $record;

    $found = false;
    foreach ($map['sources'] as &$source) {
        if ($source['id'] === $sourceId) {
            $source['verified_revision'] = $sourceReport['current_revision'];
            $source['last_decision'] = $record['id'];
            $found = true;
            break;
        }
    }
    unset($source);
    if (!$found) {
        throw new RuntimeException("Source {$sourceId} disappeared from the map.");
    }

    writeJsonAtomic($decisionPath, $ledger);
    writeJsonAtomic($root . '/contracts/larena-documentation-map.json', $map);
    writeJsonAtomic($root . '/' . $map['source_contract_file'], buildSourceContract($root, $map));

    return [
        'schema' => 'larena.documentation_decision_result.v1',
        'status' => 'accepted',
        'decision' => $record,
        'next_action' => 'Review Docara documentation status and accept changed entity bindings.',
    ];
}

/**
 * @return array{current:bool,expected_sha256:string,actual_sha256:?string}
 */
function compareJsonFile(string $path, array $expected): array
{
    $expectedText = prettyJson($expected);
    $actual = is_file($path) ? (string) file_get_contents($path) : null;

    return [
        'current' => $actual !== null && hash_equals(hash('sha256', $expectedText), hash('sha256', $actual)),
        'expected_sha256' => hash('sha256', $expectedText),
        'actual_sha256' => $actual === null ? null : hash('sha256', $actual),
    ];
}

/** @return array<string,mixed> */
function docaraStatus(string $root, bool $required): array
{
    $binary = $root . '/vendor/bin/docara';
    if (!is_file($binary)) {
        return ['status' => $required ? 'failed' : 'skipped', 'reason' => 'docara_binary_missing'];
    }
    $php = getenv('LARENA_DOC_PHP');
    if (!is_string($php) || $php === '') {
        if (version_compare(PHP_VERSION, '8.4.1', '>=')) {
            $php = PHP_BINARY;
        } else {
            $candidate = '/Applications/ServBay/package/php/8.4/8.4.20/bin/php';
            $php = is_file($candidate) ? $candidate : '';
        }
    }
    if ($php === '' || !is_file($php)) {
        return ['status' => $required ? 'failed' : 'skipped', 'reason' => 'compatible_php_missing'];
    }
    [$exit, $stdout, $stderr] = process([$php, $binary, 'documentation', 'status', '--json'], $root);
    if ($exit !== 0) {
        return ['status' => 'failed', 'reason' => 'docara_status_command_failed', 'stderr' => trim($stderr)];
    }
    $decoded = json_decode($stdout, true);
    if (!is_array($decoded)) {
        return ['status' => 'failed', 'reason' => 'docara_status_output_invalid'];
    }
    $summary = $decoded['summary'] ?? [];
    $nonCurrent = 0;
    foreach (['new', 'changed', 'missing', 'missing_example', 'unverified', 'orphan'] as $key) {
        $nonCurrent += (int) ($summary[$key] ?? 0);
    }

    return [
        'status' => $nonCurrent === 0 ? 'current' : 'failed',
        'php' => $php,
        'summary' => $summary,
        'non_current' => $nonCurrent,
    ];
}

/**
 * @param list<string> $patterns
 */
function digestRepositoryFiles(string $repository, array $patterns): string
{
    $paths = gitNullLines($repository, ['ls-files', '-z']);
    $hashes = [];
    foreach ($paths as $path) {
        if (!matchesAny($path, $patterns)) {
            continue;
        }
        $absolute = $repository . '/' . $path;
        if (is_file($absolute) && !is_link($absolute)) {
            $hashes[$path] = hash_file('sha256', $absolute);
        }
    }
    ksort($hashes, SORT_STRING);

    return hash('sha256', canonicalJson($hashes));
}

/** @return list<string> */
function dirtyPaths(string $repository): array
{
    $output = rtrim(gitOutput($repository, ['status', '--porcelain=v1', '--untracked-files=all']), "\r\n");
    $lines = $output === '' ? [] : (preg_split('/\R/', $output) ?: []);
    $paths = [];
    foreach ($lines as $line) {
        if (strlen($line) < 4) {
            continue;
        }
        $path = substr($line, 3);
        if (str_contains($path, ' -> ')) {
            $parts = explode(' -> ', $path);
            foreach ($parts as $part) {
                $paths[] = normalizePath(trim($part, '"'));
            }
            continue;
        }
        $paths[] = normalizePath(trim($path, '"'));
    }
    $paths = array_values(array_unique($paths));
    sort($paths, SORT_STRING);

    return $paths;
}

/**
 * @param list<string> $patterns
 */
function matchesAny(string $path, array $patterns): bool
{
    foreach ($patterns as $pattern) {
        if (is_string($pattern) && preg_match(globRegex($pattern), $path) === 1) {
            return true;
        }
    }

    return false;
}

function globRegex(string $pattern): string
{
    $quoted = preg_quote(normalizePath($pattern), '~');
    $quoted = str_replace('\\*\\*', '.*', $quoted);
    $quoted = str_replace('\\*', '[^/]*', $quoted);
    $quoted = str_replace('\\?', '[^/]', $quoted);

    return '~^' . $quoted . '$~';
}

function resolveRepository(string $root, string $configured): string
{
    $candidate = str_starts_with($configured, '/') ? $configured : $root . '/' . $configured;
    $real = realpath($candidate);

    return $real === false ? $candidate : $real;
}

/**
 * @param list<string> $arguments
 */
function git(string $repository, array $arguments): string
{
    return trim(gitOutput($repository, $arguments));
}

/**
 * @param list<string> $arguments
 * @return list<string>
 */
function gitLines(string $repository, array $arguments): array
{
    $output = rtrim(gitOutput($repository, $arguments), "\r\n");
    if ($output === '') {
        return [];
    }

    return array_values(array_filter(array_map('normalizePath', preg_split('/\R/', $output) ?: []), static fn (string $line): bool => $line !== ''));
}

/**
 * @param list<string> $arguments
 * @return list<string>
 */
function gitNullLines(string $repository, array $arguments): array
{
    $output = rtrim(gitOutput($repository, $arguments), "\0");
    if ($output === '') {
        return [];
    }

    return array_values(array_filter(array_map('normalizePath', explode("\0", $output)), static fn (string $line): bool => $line !== ''));
}

/**
 * @param list<string> $arguments
 */
function gitOutput(string $repository, array $arguments): string
{
    [$exit, $stdout, $stderr] = process(array_merge(['git', '-C', $repository], $arguments), $repository);
    if ($exit !== 0) {
        throw new RuntimeException('Git command failed in ' . $repository . ': ' . trim($stderr));
    }

    return $stdout;
}

/**
 * @param list<string> $command
 * @return array{0:int,1:string,2:string}
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

function normalizePath(string $path): string
{
    return str_replace('\\', '/', trim($path));
}

/** @param array<string,mixed> $value */
function writeJsonAtomic(string $path, array $value): void
{
    $directory = dirname($path);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException("Unable to create directory: {$directory}");
    }
    $temporary = $path . '.tmp.' . getmypid();
    if (file_put_contents($temporary, prettyJson($value)) === false || !rename($temporary, $path)) {
        @unlink($temporary);
        throw new RuntimeException("Unable to write JSON file: {$path}");
    }
}

function prettyJson(array $value): string
{
    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
}

function canonicalJson(array $value): string
{
    return json_encode(canonicalize($value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

function canonicalize(mixed $value): mixed
{
    if (!is_array($value)) {
        return $value;
    }
    if (array_is_list($value)) {
        return array_map('canonicalize', $value);
    }
    ksort($value, SORT_STRING);
    foreach ($value as $key => $item) {
        $value[$key] = canonicalize($item);
    }

    return $value;
}

function relativePath(string $root, string $path): string
{
    return str_starts_with($path, $root . '/') ? substr($path, strlen($root) + 1) : $path;
}

/** @param array<string,mixed> $payload */
function output(array $payload): void
{
    echo prettyJson($payload);
}
