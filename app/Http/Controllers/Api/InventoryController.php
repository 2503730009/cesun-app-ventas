<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->select(['id', 'name', 'stock', 'price'])
            ->orderBy('id')
            ->paginate(10);

        $data = $products->getCollection()->transform(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'stock' => $product->stock,
            'price' => (string) $product->price,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock,
                'price' => (string) $product->price,
            ],
        ]);
    }

    public function update(UpdateInventoryRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock,
                'price' => (string) $product->price,
            ],
        ]);
    }
}
