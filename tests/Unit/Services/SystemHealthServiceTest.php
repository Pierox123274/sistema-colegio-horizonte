<?php

namespace Tests\Unit\Services;

use App\Services\SystemHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SystemHealthServiceTest extends TestCase
{
    use RefreshDatabase;

    private SystemHealthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SystemHealthService::class);
    }

    public function test_health_snapshot_includes_database_and_integration_checks(): void
    {
        Config::set('app.debug', false);
        Config::set('queue.default', 'database');

        $snapshot = $this->service->healthSnapshot();

        $this->assertArrayHasKey('status', $snapshot);
        $this->assertArrayHasKey('checks', $snapshot);
        $this->assertSame('ok', $snapshot['checks']['database']['status']);
        $this->assertArrayHasKey('integrations', $snapshot);
        $this->assertTrue($snapshot['storage']['is_storage_writable']);
    }

    public function test_health_snapshot_flags_debug_and_scheduler_issues(): void
    {
        Config::set('app.debug', true);
        Config::set('queue.default', 'sync');
        Cache::forget('system.scheduler.last_run_at');

        $snapshot = $this->service->healthSnapshot();

        $this->assertSame('critical', $snapshot['checks']['app_debug']['status']);
        $this->assertSame('warning', $snapshot['checks']['queue_connection']['status']);
        $this->assertSame('warning', $snapshot['checks']['scheduler']['status']);
        $this->assertContains($snapshot['status'], ['warning', 'critical']);
    }

    public function test_health_snapshot_marks_scheduler_healthy_with_recent_heartbeat(): void
    {
        Config::set('app.debug', false);
        Cache::put('system.scheduler.last_run_at', now()->toIso8601String(), 600);

        $snapshot = $this->service->healthSnapshot();

        $this->assertSame('ok', $snapshot['checks']['scheduler']['status']);
    }

    public function test_failed_jobs_paginator_returns_empty_when_table_missing(): void
    {
        $paginator = $this->service->failedJobsPaginator();

        $this->assertSame(0, $paginator->total());
    }
}
