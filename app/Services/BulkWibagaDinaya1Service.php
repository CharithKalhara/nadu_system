<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Nadu;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class BulkWibagaDinaya1Service extends BulkThinduwaWrittenService
{
	protected function templatePath(): string
	{
		return storage_path('app/documents/1_wibaga_dinaya.docx');
	}

	protected function outputDirectoryName(): string
	{
		return 'wibaga-dinaya-1';
	}

	protected function filePrefix(): string
	{
		return 'wibaga_dinaya_1';
	}

	protected function documentType(): string
	{
		return 'Bulk 1 Wibaga Dinaya';
	}

	protected function fillTemplate(TemplateProcessor $t, Nadu $c, array $v = []): void
	{
		$company = Company::find(session('company_id'));

		$t->setValue('1_wibaga_dinaya_block', '');
		$t->setValue('/1_wibaga_dinaya_block', '');

		$t->setValue('ණයකරු_1', $c->nayakaru1_nama ?? '');
		$t->setValue('ණයකරු_2', $c->nayakaru2_nama ?? '');
		$t->setValue('ඇපකරු_1', $c->aepakaru1_nama ?? '');
		$t->setValue('ඇපකරු_2', $c->aepakaru2_nama ?? '');

		// Company-level/template values that the single-case generator sets.
		$t->setValue('1_විභාග_දිනය', $company?->first_sithasiya_date ? Carbon::parse($company->first_sithasiya_date)->format('Y/m/d') : '');
		$t->setValue('2_විභාග_දිනය', $company?->second_sithasiya_date ? Carbon::parse($company->second_sithasiya_date)->format('Y/m/d') : '');
		$t->setValue('ස්ථානය', $company->wibaga_sthanaya ?? '');
		$t->setValue('නියෝජිතයා', $company->niyojithaya ?? '');
	}
}
