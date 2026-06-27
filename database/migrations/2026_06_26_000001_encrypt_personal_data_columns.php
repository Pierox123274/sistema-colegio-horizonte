<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropUnique(['document_number']);
            $table->string('document_number_hash', 64)->nullable()->after('document_number');
            $table->unique('document_number_hash');
        });

        Schema::table('guardians', function (Blueprint $table): void {
            $table->dropUnique(['document_number']);
            $table->string('document_number_hash', 64)->nullable()->after('document_number');
            $table->unique('document_number_hash');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE students MODIFY document_number TEXT NULL');
            DB::statement('ALTER TABLE students MODIFY address TEXT NULL');
            DB::statement('ALTER TABLE students MODIFY phone TEXT NULL');
            DB::statement('ALTER TABLE students MODIFY email TEXT NULL');
            DB::statement('ALTER TABLE guardians MODIFY document_number TEXT NULL');
            DB::statement('ALTER TABLE guardians MODIFY phone TEXT NULL');
            DB::statement('ALTER TABLE guardians MODIFY secondary_phone TEXT NULL');
            DB::statement('ALTER TABLE guardians MODIFY email TEXT NULL');
            DB::statement('ALTER TABLE guardians MODIFY address TEXT NULL');
            DB::statement('ALTER TABLE guardians MODIFY workplace TEXT NULL');
            DB::statement('ALTER TABLE virtual_meetings MODIFY join_password TEXT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropUnique(['document_number_hash']);
            $table->dropColumn('document_number_hash');
            $table->unique('document_number');
        });

        Schema::table('guardians', function (Blueprint $table): void {
            $table->dropUnique(['document_number_hash']);
            $table->dropColumn('document_number_hash');
            $table->unique('document_number');
        });
    }
};
