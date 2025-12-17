<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\JsonResponse;

class BusinessSettingController extends Controller
{
    /**
     * GET /api/v1/settings/fees-points
     */
    public function fees(): JsonResponse
    {
        $settings = BusinessSetting::whereIn('type', [
            'service_fee',
        ])->pluck('value', 'type');

        return response()->json([
            'service_fee'           => $settings['service_fee']           ?? null,
        ]);
    }
}
