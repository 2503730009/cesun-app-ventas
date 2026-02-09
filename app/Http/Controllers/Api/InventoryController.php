<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->select(['id', 'name', 'stock', 'price'])
            ->orderBy('id')
            ->paginate($this->perPage($request));

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

    private function perPage(Request $request): int
    {
        $perPage = $request->query('per_page', 10);

        if (!is_numeric($perPage) || (int) $perPage <= 0) {
            return 10;
        }

        return min((int) $perPage, 100);
    }
}
