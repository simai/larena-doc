#!/usr/bin/env php
<?php

declare(strict_types=1);

$project = sys_get_temp_dir() . '/larena-docara-status-' . bin2hex(random_bytes(5));
$upstream = $project . '/upstream';

try {
    mkdir($project . '/scripts', 0775, true);
    mkdir($upstream, 0775, true);
    copy(dirname(__DIR__) . '/scripts/docara-release-status.php', $project . '/scripts/docara-release-status.php');

    run(['git', 'init', '-q', '-b', 'main'], $upstream);
    run(['git', 'config', 'user.name', 'Docara Release Test'], $upstream);
    run(['git', 'config', 'user.email', 'release-test@example.invalid'], $upstream);
    file_put_contents($upstream . '/runtime.php', "<?php\n");
    run(['git', 'add', 'runtime.php'], $upstream);
    run(['git', 'commit', '-q', '-m', 'stable'], $upstream);
    $v290 = trim(run(['git', 'rev-parse', 'HEAD'], $upstream)[1]);
    run(['git', 'tag', 'v2.9.0'], $upstream);

    file_put_contents($upstream . '/runtime.php', "<?php\n// fixed\n");
    run(['git', 'add', 'runtime.php'], $upstream);
    run(['git', 'commit', '-q', '-m', 'runtime fix'], $upstream);
    $v291 = trim(run(['git', 'rev-parse', 'HEAD'], $upstream)[1]);

    writeLock($project, '2.9.0', $v290, $upstream);
    [$exit, $stdout] = run([
        PHP_BINARY, $project . '/scripts/docara-release-status.php',
        '--require-current', '--repository=' . $upstream,
    ], $project, false);
    $stale = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame(1, $exit, 'unreleased change exit');
    assertSame('stale', $stale['status'], 'unreleased change status');
    assertSame(['unreleased_docara_changes_on_main'], $stale['errors'], 'unreleased change reason');

    run(['git', 'tag', 'v2.9.1'], $upstream);
    writeLock($project, '2.9.1', $v291, $upstream);
    [$exit, $stdout] = run([
        PHP_BINARY, $project . '/scripts/docara-release-status.php',
        '--require-current', '--repository=' . $upstream,
    ], $project, false);
    $current = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame(0, $exit, 'released current exit');
    assertSame('current', $current['status'], 'released current status');

    mkdir($upstream . '/.github', 0775, true);
    file_put_contents($upstream . '/.github/release-request.json', "{}\n");
    run(['git', 'add', '.github/release-request.json'], $upstream);
    run(['git', 'commit', '-q', '-m', 'publish release'], $upstream);
    [$exit, $stdout] = run([
        PHP_BINARY, $project . '/scripts/docara-release-status.php',
        '--require-current', '--repository=' . $upstream,
    ], $project, false);
    $published = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    assertSame(0, $exit, 'publication marker exit');
    assertSame('current', $published['status'], 'publication marker ignored');

    echo json_encode([
        'schema' => 'larena.docara_release_status_test.v1',
        'status' => 'passed',
        'assertions' => 7,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
} finally {
    removeTree($project);
}

function writeLock(string $project, string $version, string $revision, string $repository): void
{
    file_put_contents($project . '/composer.lock', json_encode([
        'packages' => [[
            'name' => 'simai/docara',
            'version' => 'v' . $version,
            'source' => ['type' => 'git', 'url' => $repository, 'reference' => $revision],
        ]],
        'packages-dev' => [],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n");
}

/** @param list<string> $command
 *  @return array{0:int,1:string,2:string}
 */
function run(array $command, string $cwd, bool $throw = true): array
{
    $pipes = [];
    $process = proc_open($command, [
        0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w'],
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

function assertSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': expected ' . var_export($expected, true)
            . ', got ' . var_export($actual, true));
    }
}

function removeTree(string $path): void
{
    if (!is_dir($path)) {
        return;
    }
    foreach (scandir($path) ?: [] as $item) {
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
