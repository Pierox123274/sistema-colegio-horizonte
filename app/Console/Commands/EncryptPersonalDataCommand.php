<?php

namespace App\Console\Commands;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\VirtualMeeting;
use App\Support\EncryptedValue;
use App\Support\SensitiveDataHasher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptPersonalDataCommand extends Command
{
    protected $signature = 'data:encrypt-personal {--force : Re-encrypt even if values look encrypted}';

    protected $description = 'Cifra datos personales existentes (documento, contacto, contraseñas de reunión) con APP_KEY';

    public function handle(): int
    {
        if (! config('app.key')) {
            $this->error('APP_KEY no está configurada. Ejecute php artisan key:generate.');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $students = $this->encryptStudents($force);
        $guardians = $this->encryptGuardians($force);
        $meetings = $this->encryptMeetings($force);

        $this->info("Estudiantes cifrados: {$students}");
        $this->info("Apoderados cifrados: {$guardians}");
        $this->info("Reuniones cifradas: {$meetings}");

        return self::SUCCESS;
    }

    private function encryptStudents(bool $force): int
    {
        $count = 0;

        Student::query()->orderBy('id')->chunkById(100, function ($rows) use (&$count, $force): void {
            foreach ($rows as $student) {
                $raw = DB::table('students')->where('id', $student->id)->first();
                if ($raw === null) {
                    continue;
                }

                $updates = $this->buildEncryptedUpdates(
                    (array) $raw,
                    ['document_number', 'address', 'phone', 'email', 'medical_observations'],
                    $force,
                );

                if ($updates === []) {
                    continue;
                }

                if (array_key_exists('document_number', $updates)) {
                    $updates['document_number_hash'] = SensitiveDataHasher::hashDocument($updates['document_number']);
                }

                $student->fill($updates);
                $student->saveQuietly();
                $count++;
            }
        });

        return $count;
    }

    private function encryptGuardians(bool $force): int
    {
        $count = 0;

        Guardian::query()->orderBy('id')->chunkById(100, function ($rows) use (&$count, $force): void {
            foreach ($rows as $guardian) {
                $raw = DB::table('guardians')->where('id', $guardian->id)->first();
                if ($raw === null) {
                    continue;
                }

                $updates = $this->buildEncryptedUpdates(
                    (array) $raw,
                    ['document_number', 'phone', 'secondary_phone', 'email', 'address', 'workplace'],
                    $force,
                );

                if ($updates === []) {
                    continue;
                }

                if (array_key_exists('document_number', $updates)) {
                    $updates['document_number_hash'] = SensitiveDataHasher::hashDocument($updates['document_number']);
                }

                $guardian->fill($updates);
                $guardian->saveQuietly();
                $count++;
            }
        });

        return $count;
    }

    private function encryptMeetings(bool $force): int
    {
        $count = 0;

        VirtualMeeting::query()->orderBy('id')->chunkById(100, function ($rows) use (&$count, $force): void {
            foreach ($rows as $meeting) {
                $raw = DB::table('virtual_meetings')->where('id', $meeting->id)->first();
                if ($raw === null || $raw->join_password === null || $raw->join_password === '') {
                    continue;
                }

                if (! $force && EncryptedValue::isEncrypted($raw->join_password)) {
                    continue;
                }

                $meeting->join_password = $raw->join_password;
                $meeting->saveQuietly();
                $count++;
            }
        });

        return $count;
    }

    /**
     * @param  list<string>  $fields
     * @return array<string, mixed>
     */
    private function buildEncryptedUpdates(array $raw, array $fields, bool $force): array
    {
        $updates = [];

        foreach ($fields as $field) {
            $value = $raw[$field] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            if (! $force && EncryptedValue::isEncrypted((string) $value)) {
                continue;
            }

            $updates[$field] = $value;
        }

        return $updates;
    }
}
