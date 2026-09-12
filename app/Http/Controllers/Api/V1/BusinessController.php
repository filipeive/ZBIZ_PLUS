<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusinessController extends Controller
{
    /**
     * Public business info endpoint — no auth required.
     */
    public function businessInfo(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID');
        $slug     = $request->header('X-Tenant-Slug');

        $tenant = null;
        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
        } elseif ($slug) {
            $tenant = Tenant::where('slug', $slug)->first();
        }

        if (!$tenant) {
            return response()->json(['message' => 'Negócio não encontrado.'], 404);
        }

        return response()->json([
            'name'          => $tenant->name,
            'slug'          => $tenant->slug,
            'business_type' => $tenant->business_type,
            'phone'         => $tenant->phone,
            'email'         => $tenant->email,
            'address'       => $tenant->address,
            'currency'      => $tenant->currency,
        ]);
    }
}
