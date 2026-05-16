<?php

namespace Tests\Feature\System;

use App\Enums\IntranetRole;
use App\Jobs\CreateInstitutionalBackupJob;
use App\Jobs\InstitutionMetricsSnapshotJob;
use App\Models\User;
use App\Services\InstitutionBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DevOpsInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->syncRoles([IntranetRole::Administrador->value]);

        return $u;
    }

    private function secretaria(): User
    {
        $u = User::factory()->create();
        $u->syncRoles([IntranetRole::Secretaria->value]);

        return $u;
    }

    public function test_schedule_lists_institution_tasks(): void
    {
        Artisan::call('schedule:list');
        $output = Artisan::output();
        $this->assertStringContainsString('institution:', $output);
    }

    public function test_admin_can_view_system_health(): void
    {
        $this->actingAs($this->admin())
            ->get(route('intranet.system.health.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/System/Health')
                ->has('health.database')
                ->has('health.queue'));
    }

    public function test_secretaria_cannot_access_system_routes(): void
    {
        $this->actingAs($this->secretaria())
            ->get(route('intranet.system.health.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_jobs_and_backups_pages(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('intranet.system.jobs.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Intranet/System/Jobs'));

        $this->actingAs($admin)
            ->get(route('intranet.system.backups.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Intranet/System/Backups'));
    }

    public function test_institution_validate_environment_command(): void
    {
        $this->artisan('institution:validate-environment')->assertSuccessful();
    }

    public function test_institution_purge_audit_logs_command_runs(): void
    {
        $this->artisan('institution:purge-old-audit-logs', ['--days' => '3650'])->assertSuccessful();
    }

    public function test_metrics_snapshot_job_populates_cache(): void
    {
        $job = new InstitutionMetricsSnapshotJob;
        $job->handle();

        $this->assertNotNull(cache()->get(config('devops.metrics_cache_key')));
    }

    public function test_create_backup_job_runs_without_error_on_sqlite(): void
    {
        if (! class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive no disponible.');
        }

        $job = new CreateInstitutionalBackupJob;
        $job->handle(app(InstitutionBackupService::class));

        $this->assertNotEmpty(File::glob(storage_path('app/backups/*.zip')));
    }

    public function test_admin_can_queue_backup_via_http(): void
    {
        $this->actingAs($this->admin())
            ->post(route('intranet.system.backups.store'))
            ->assertRedirect();
    }
}
