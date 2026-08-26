<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->changeAmountsToDecimal();
    }

    public function down(): void
    {
        DB::table('companies')->select('table_name')->orderBy('id')->each(function (object $company): void {
            $tableName = $company->table_name;
            $schema = Schema::connection('companies');

            if (! preg_match('/^company_\d{4}_cases$/', $tableName) || ! $schema->hasTable($tableName)) {
                return;
            }

            $schema->table($tableName, function (Blueprint $table): void {
                $table->integer('apal_pa')->nullable()->change();
                $table->integer('apal_vi')->nullable()->change();
            });
        });
    }

    private function changeAmountsToDecimal(): void
    {
        DB::table('companies')->select('table_name')->orderBy('id')->each(function (object $company): void {
            $tableName = $company->table_name;
            $schema = Schema::connection('companies');

            if (! preg_match('/^company_\d{4}_cases$/', $tableName) || ! $schema->hasTable($tableName)) {
                return;
            }

            $schema->table($tableName, function (Blueprint $table): void {
                $table->decimal('apal_pa', 15, 2)->nullable()->change();
                $table->decimal('apal_vi', 15, 2)->nullable()->change();
            });
        });
    }
};
