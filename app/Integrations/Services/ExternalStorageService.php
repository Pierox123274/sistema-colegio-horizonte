<?php

namespace App\Integrations\Services;

final class ExternalStorageService
{
    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        $disk = (string) config('filesystems.default');
        $driver = config("filesystems.disks.{$disk}.driver", $disk);

        return [
            'disk' => $disk,
            'driver' => $driver,
            'external_enabled' => (bool) config('integrations.storage.external_enabled'),
            's3_configured' => $this->diskConfigured('s3'),
            'recommended_env' => 'FILESYSTEM_DISK=s3 con AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_BUCKET',
        ];
    }

    private function diskConfigured(string $disk): bool
    {
        return (bool) config("filesystems.disks.{$disk}.key")
            && (bool) config("filesystems.disks.{$disk}.bucket");
    }
}
