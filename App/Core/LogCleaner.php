<?php

namespace App\Core;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

final class LogCleaner
{
    public function clean(int $retentionDays = 30, bool $delete = false): array
    {
        if ($retentionDays < 1) {
            throw new \InvalidArgumentException('O período de retenção deve ser de pelo menos um dia.');
        }

        $baseDirectory = Config::getPathLogs();

        if (! is_dir($baseDirectory)) {
            return $this->result($retentionDays, $delete, false);
        }

        $root = realpath($baseDirectory);
        if ($root === false) {
            throw new RuntimeException('Não foi possível resolver o diretório de logs.');
        }

        $result = $this->result($retentionDays, $delete, true);
        $cutoff = time() - ($retentionDays * 86400);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo
                || $file->isLink()
                || ! $file->isFile()
                || strtolower($file->getExtension()) !== 'log') {
                continue;
            }

            $path = $file->getRealPath();
            if ($path === false || ! $this->isWithin($path, $root)) {
                $result['skipped_unsafe']++;
                continue;
            }

            $result['scanned']++;
            if ($file->getMTime() >= $cutoff) {
                continue;
            }

            $result['eligible']++;
            if (! $delete) {
                continue;
            }

            if (@unlink($path)) {
                $result['deleted']++;
            } else {
                $result['failed'][] = $path;
            }
        }

        return $result;
    }

    private function result(int $retentionDays, bool $delete, bool $directoryExists): array
    {
        return [
            'mode' => $delete ? 'delete' : 'dry-run',
            'retention_days' => $retentionDays,
            'directory_exists' => $directoryExists,
            'scanned' => 0,
            'eligible' => 0,
            'deleted' => 0,
            'skipped_unsafe' => 0,
            'failed' => [],
        ];
    }

    private function isWithin(string $path, string $root): bool
    {
        $normalizedPath = strtolower(str_replace('\\', '/', $path));
        $normalizedRoot = rtrim(strtolower(str_replace('\\', '/', $root)), '/') . '/';

        return str_starts_with($normalizedPath, $normalizedRoot);
    }
}
