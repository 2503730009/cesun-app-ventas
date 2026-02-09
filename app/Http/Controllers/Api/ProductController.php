<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->select(['id', 'vendor_id', 'name', 'description', 'price', 'stock', 'created_at'])
            ->with(['vendor:id,name,email,phone,created_at'])
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'ok' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Basic XSS prevention for free-text inputs.
        $data['description'] = $this->sanitizeText($data['description'] ?? null);

        $product = Product::create($data);

        return response()->json([
            'ok' => true,
            'data' => new ProductResource($product->load(['vendor:id,name,email,phone,created_at'])),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => new ProductResource($product->load(['vendor:id,name,email,phone,created_at'])),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        // Basic XSS prevention for free-text inputs.
        if (array_key_exists('description', $data)) {
            $data['description'] = $this->sanitizeText($data['description']);
        }

        $product->update($data);

        return response()->json([
            'ok' => true,
            'data' => new ProductResource($product->load(['vendor:id,name,email,phone,created_at'])),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Product deleted',
        ]);
    }

    private function sanitizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(strip_tags($value));
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
