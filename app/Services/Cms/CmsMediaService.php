<?php

namespace App\Services\Cms;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Models\Cms\CmsMedia;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CmsMediaService
{
    private const UPLOAD_DIR = 'cms';

    public function __construct(
        private readonly AuditService $audit,
    ) {}

    /**
     * @param  array<string, string>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 24): LengthAwarePaginator
    {
        $query = CmsMedia::query()
            ->with('uploadedBy:id,name')
            ->orderByDesc('created_at');

        if ($search = trim($filters['search'] ?? '')) {
            $query->where(function ($q) use ($search): void {
                $q->where('filename', 'like', "%{$search}%")
                    ->orWhere('alt', 'like', "%{$search}%")
                    ->orWhere('path', 'like', "%{$search}%");
            });
        }

        if (($mime = $filters['mime'] ?? '') === 'image') {
            $query->where('mime', 'like', 'image/%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function browseItems(LengthAwarePaginator $paginator): array
    {
        return $paginator->getCollection()
            ->map(fn (CmsMedia $media) => $this->toPickerArray($media))
            ->values()
            ->all();
    }

    public function store(UploadedFile $file, ?User $uploader = null, ?string $alt = null): CmsMedia
    {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs(self::UPLOAD_DIR, $filename, 'public');

        $media = CmsMedia::query()->create([
            'path' => $path,
            'disk' => 'public',
            'filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt' => $alt,
            'uploaded_by_user_id' => $uploader?->id,
        ]);

        if ($uploader !== null) {
            $this->audit->log(
                action: AuditAction::Create,
                module: AuditModule::Cms,
                user: $uploader,
                entityType: CmsMedia::class,
                entityId: $media->id,
                description: 'Medio CMS subido: '.$media->filename,
                newValues: ['path' => $media->path, 'mime' => $media->mime],
            );
        }

        return $media;
    }

    public function destroy(CmsMedia $media, ?User $actor = null): void
    {
        $path = $media->path;

        if ($media->path !== '' && Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();

        if ($actor !== null) {
            $this->audit->log(
                action: AuditAction::Delete,
                module: AuditModule::Cms,
                user: $actor,
                entityType: CmsMedia::class,
                entityId: $media->id,
                description: 'Medio CMS eliminado',
                oldValues: ['path' => $path],
            );
        }
    }

    public function publicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Str::startsWith($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/'.$path);
    }

    /**
     * @return array<string, mixed>
     */
    public function toPickerArray(CmsMedia $media): array
    {
        return [
            'id' => $media->id,
            'path' => $media->path,
            'url' => $this->publicUrl($media->path),
            'filename' => $media->filename,
            'mime' => $media->mime,
            'size' => $media->size,
            'size_label' => $this->humanSize((int) $media->size),
            'alt' => $media->alt,
            'is_image' => str_starts_with((string) $media->mime, 'image/'),
            'created_at' => $media->created_at?->toIso8601String(),
        ];
    }

    public function humanSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }

    /**
     * @param  Collection<int, CmsMedia>  $items
     * @return list<array<string, mixed>>
     */
    public function mapCollection(Collection $items): array
    {
        return $items->map(fn (CmsMedia $m) => $this->toPickerArray($m))->values()->all();
    }
}
