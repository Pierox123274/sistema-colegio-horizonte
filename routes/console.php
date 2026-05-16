<?php

use App\Jobs\AcademicAlertScanJob;
use App\Jobs\CreateInstitutionalBackupJob;
use App\Jobs\FinancialAlertScanJob;
use App\Jobs\GenerateInstitutionInsightsJob;
use App\Jobs\InstitutionMetricsSnapshotJob;
use App\Jobs\SecurityHealthScanJob;
use App\Jobs\SendInstitutionDailySummaryJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Programación institucional (equivalente histórico a app/Console/Kernel.php)
|--------------------------------------------------------------------------
| En producción: `* * * * * cd /ruta && php artisan schedule:run >> /dev/null 2>&1`
| O servicio Docker: `php artisan schedule:work`
*/
Schedule::command('institution:purge-old-audit-logs')->dailyAt('02:10');
Schedule::command('institution:purge-expired-user-sessions')->everySixHours();
Schedule::command('institution:prune-old-backups')->dailyAt('04:05');
Schedule::job(new CreateInstitutionalBackupJob)->dailyAt('03:15');
Schedule::job(new SendInstitutionDailySummaryJob)->dailyAt('07:30');
Schedule::job(new AcademicAlertScanJob)->weekdays()->at('06:40');
Schedule::job(new FinancialAlertScanJob)->weekdays()->at('06:55');
Schedule::job(new SecurityHealthScanJob)->dailyAt('05:40');
Schedule::job(new InstitutionMetricsSnapshotJob)->hourly();
Schedule::job(new GenerateInstitutionInsightsJob)->dailyAt('07:50');
