<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with('vendor')
            ->latest()
            ->paginate(10);

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
        $product = Product::create($request->validated());

        return response()->json([
            'ok' => true,
            'data' => new ProductResource($product->load('vendor')),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => new ProductResource($product->load('vendor')),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'ok' => true,
            'data' => new ProductResource($product->load('vendor')),
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
}
