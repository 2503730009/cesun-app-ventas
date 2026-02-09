<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendors = Vendor::query()
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'ok' => true,
            'data' => VendorResource::collection($vendors),
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
            ],
        ]);
    }

    public function store(StoreVendorRequest $request): JsonResponse
    {
        $vendor = Vendor::create($request->validated());

        return response()->json([
            'ok' => true,
            'data' => new VendorResource($vendor),
        ], 201);
    }

    public function show(Vendor $vendor): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => new VendorResource($vendor),
        ]);
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor): JsonResponse
    {
        $vendor->update($request->validated());

        return response()->json([
            'ok' => true,
            'data' => new VendorResource($vendor),
        ]);
    }

    public function destroy(Vendor $vendor): JsonResponse
    {
        $vendor->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Vendor deleted',
        ]);
    }

    private function perPage(Request $request): int
    {
        $perPage = $request->query('per_page', 10);

        if (!is_numeric($perPage) || (int) $perPage <= 0) {
            return 10;
        }

        return min((int) $perPage, 100);
    }
}
