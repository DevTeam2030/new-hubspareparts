<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Http\Resources\GovernorateResource;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    /**
     * GET /api/v1/governorates
     */
    public function index()
    {
        $govs = Governorate::with(['areas','deliveryTimes'])->get();
        return GovernorateResource::collection($govs);
    }

    /**
     * GET /api/v1/governorates/{id}
     */
    public function show($id)
    {
        $gov = Governorate::with(['areas','deliveryTimes'])->findOrFail($id);
        return new GovernorateResource($gov);
    }
}
