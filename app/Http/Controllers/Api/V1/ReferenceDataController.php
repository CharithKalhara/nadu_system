<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\MobileCaseFieldCatalog;
use Illuminate\Http\JsonResponse;

class ReferenceDataController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'case_form_version' => 1,
                'sections' => MobileCaseFieldCatalog::sections(),
            ],
        ]);
    }
}
