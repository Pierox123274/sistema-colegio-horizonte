<?php

namespace App\Services\Cms;

use App\Models\Cms\CmsSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class CmsSettingService
{
    private const CACHE_KEY = 'cms:settings:all';

    private const CACHE_TTL = 3600;

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return $all->get($key, $default);
    }

    public function set(string $key, mixed $value): CmsSetting
    {
        $setting = CmsSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? $value : ['value' => $value]],
        );

        $this->clearCache();

        return $setting;
    }

    /**
     * @return Collection<string, mixed>
     */
    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): Collection {
            return CmsSetting::query()
                ->orderBy('key')
                ->get()
                ->mapWithKeys(fn (CmsSetting $s): array => [$s->key => $s->value]);
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        app(CmsPublicService::class)->clearCache();
    }
}
