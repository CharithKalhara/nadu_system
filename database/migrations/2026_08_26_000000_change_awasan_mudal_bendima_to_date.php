<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('companies')
            ->select('table_name')
            ->orderBy('id')
            ->each(function (object $company): void {
                $tableName = $company->table_name;

                if (! preg_match('/^company_\d{4}_cases$/', $tableName)
                    || ! Schema::connection('companies')->hasTable($tableName)
                    || ! Schema::connection('companies')->hasColumn($tableName, 'awasan_mudal_bendima')) {
                    return;
                }

                Schema::connection('companies')->table($tableName, function (Blueprint $table): void {
                    $table->date('awasan_mudal_bendima')->nullable()->change();
                });
            });
    }

    public function down(): void
    {
        DB::table('companies')
            ->select('table_name')
            ->orderBy('id')
            ->each(function (object $company): void {
                $tableName = $company->table_name;

                if (! preg_match('/^company_\d{4}_cases$/', $tableName)
                    || ! Schema::connection('companies')->hasTable($tableName)
                    || ! Schema::connection('companies')->hasColumn($tableName, 'awasan_mudal_bendima')) {
                    return;
                }

                Schema::connection('companies')->table($tableName, function (Blueprint $table): void {
                    $table->decimal('awasan_mudal_bendima', 15, 2)->nullable()->change();
                });
            });
    }
};
