<?php

namespace App\Console\Commands;

use App\Support\EnvSecurityValidator;
use Illuminate\Console\Command;

class InstitutionValidateEnvironmentCommand extends Command
{
    protected $signature = 'institution:validate-environment {--strict-production : Reglas adicionales si APP_ENV=production}';

    protected $description = 'Valida variables de entorno críticas para despliegue seguro.';

    public function handle(EnvSecurityValidator $validator): int
    {
        $strict = (bool) $this->option('strict-production');
        $issues = $validator->validate($strict);

        if ($issues === []) {
            $this->info('Entorno OK.');

            return self::SUCCESS;
        }

        foreach ($issues as $issue) {
            $this->warn($issue);
        }

        return self::FAILURE;
    }
}
