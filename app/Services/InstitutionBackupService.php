<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use ZipArchive;

final class InstitutionBackupService
{
    public function backupDirectory(): string
    {
        $dir = storage_path('app/backups');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        return $dir;
    }

    /**
     * @return list<array{name: string, path: string, size_bytes: int, modified_at: string}>
     */
    public function listBackups(): array
    {
        $dir = $this->backupDirectory();
        $files = File::glob($dir.DIRECTORY_SEPARATOR.'*');
        if ($files === false) {
            return [];
        }

        $items = [];
        foreach ($files as $path) {
            if (! is_file($path)) {
                continue;
            }
            $name = basename($path);
            if (str_starts_with($name, '.')) {
                continue;
            }
            $items[] = [
                'name' => $name,
                'path' => $path,
                'size_bytes' => (int) filesize($path),
                'modified_at' => date('c', (int) filemtime($path)),
            ];
        }

        usort($items, fn (array $a, array $b): int => strcmp($b['modified_at'], $a['modified_at']));

        return $items;
    }

    /**
     * Crea un respaldo (SQLite o MySQL vía mysqldump si está disponible) y opcionalmente public/.
     *
     * @return string Ruta del archivo principal creado
     */
    public function createBackupArchive(): string
    {
        $dir = $this->backupDirectory();
        $stamp = now()->format('Ymd-His');
        $zipPath = $dir.DIRECTORY_SEPARATOR."institution-{$stamp}.zip";

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('No se pudo crear el archivo ZIP de respaldo.');
        }

        $driver = config('database.default');
        $connection = config("database.connections.{$driver}");

        if ($driver === 'sqlite') {
            $databasePath = $connection['database'] ?? database_path('database.sqlite');
            if ($databasePath === ':memory:') {
                $zip->addFromString(
                    'database/README.txt',
                    "Base SQLite en memoria (:memory:): no hay archivo físico para respaldar en este entorno.\n",
                );
            } elseif (is_file($databasePath)) {
                $contents = @file_get_contents($databasePath);
                if ($contents !== false) {
                    $zip->addFromString('database/database.sqlite', $contents);
                }
            }
        } elseif ($driver === 'mysql' && is_string($connection['database'] ?? null)) {
            $dump = $this->tryMysqlDump($connection);
            if ($dump !== null) {
                $zip->addFromString('database/dump.sql', $dump);
            } else {
                Log::warning('InstitutionBackupService: mysqldump no disponible o falló; ZIP sin volcado SQL.');
            }
        }

        if (config('devops.backup_include_public', true) && File::isDirectory(storage_path('app/public'))) {
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'public');
        }

        $zip->close();

        $this->pruneOldBackups((int) config('devops.backup_max_files', 14));

        return $zipPath;
    }

    public function pruneOldBackups(int $keep): void
    {
        $items = $this->listBackups();
        foreach (array_slice($items, $keep) as $row) {
            if (File::exists($row['path'])) {
                File::delete($row['path']);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function tryMysqlDump(array $connection): ?string
    {
        $binary = (string) config('devops.mysqldump_path', 'mysqldump');
        $database = $connection['database'] ?? '';
        $username = $connection['username'] ?? '';
        $password = $connection['password'] ?? '';
        $host = $connection['host'] ?? '127.0.0.1';
        $port = (string) ($connection['port'] ?? 3306);

        if ($database === '') {
            return null;
        }

        $command = [
            $binary,
            '--no-tablespaces',
            '--single-transaction',
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            $database,
        ];

        $env = [];
        if ($password !== '') {
            $env['MYSQL_PWD'] = $password;
        }

        try {
            $result = Process::env($env)->timeout(600)->run($command);

            return $result->successful() ? $result->output() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function addDirectoryToZip(ZipArchive $zip, string $absolutePath, string $zipPrefix): void
    {
        $files = File::allFiles($absolutePath);
        foreach ($files as $file) {
            $relative = $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $zipPrefix.'/'.$relative);
        }
    }
}
