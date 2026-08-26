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
                $schema = Schema::connection('companies');

                if (! preg_match('/^company_\d{4}_cases$/', $tableName)
                    || ! $schema->hasTable($tableName)) {
                    return;
                }

                if (! $schema->hasColumn($tableName, 'apal_pa')) {
                    $schema->table($tableName, function (Blueprint $table): void {
                        $table->decimal('apal_pa', 15, 2)->nullable();
                    });
                }

                if (! $schema->hasColumn($tableName, 'apal_vi')) {
                    $schema->table($tableName, function (Blueprint $table): void {
                        $table->decimal('apal_vi', 15, 2)->nullable();
                    });
                }
            });
    }

    public function down(): void
    {
        DB::table('companies')
            ->select('table_name')
            ->orderBy('id')
            ->each(function (object $company): void {
                $tableName = $company->table_name;
                $schema = Schema::connection('companies');

                if (! preg_match('/^company_\d{4}_cases$/', $tableName)
                    || ! $schema->hasTable($tableName)) {
                    return;
                }

                $columns = array_filter([
                    $schema->hasColumn($tableName, 'apal_pa') ? 'apal_pa' : null,
                    $schema->hasColumn($tableName, 'apal_vi') ? 'apal_vi' : null,
                ]);

                if ($columns !== []) {
                    $schema->table($tableName, function (Blueprint $table) use ($columns): void {
                        $table->dropColumn($columns);
                    });
                }
            });
    }
};
