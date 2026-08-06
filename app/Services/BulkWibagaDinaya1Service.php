<?php
namespace App\Services;
use App\Models\Nadu; use PhpOffice\PhpWord\TemplateProcessor;
class BulkWibagaDinaya1Service extends BulkThinduwaWrittenService { protected function templatePath(): string{return storage_path('app/documents/1_wibaga_dinaya.docx');} protected function outputDirectoryName(): string{return 'wibaga-dinaya-1';} protected function filePrefix(): string{return 'wibaga_dinaya_1';} protected function documentType(): string{return 'Bulk 1 Wibaga Dinaya';} protected function fillTemplate(TemplateProcessor $t,Nadu $c,array $v=[]):void{foreach(['1_wibaga_dinaya_block'=>'','/1_wibaga_dinaya_block'=>'','ණයකරු_1'=>$c->nayakaru1_nama,'ණයකරු_2'=>$c->nayakaru2_nama,'ඇපකරු_1'=>$c->aepakaru1_nama,'ඇපකරු_2'=>$c->aepakaru2_nama] as $k=>$x)$t->setValue($k,$x??'');}}
