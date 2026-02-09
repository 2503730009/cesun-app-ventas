<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->select(['id', 'vendor_id', 'status', 'total', 'customer_name', 'customer_phone'])
            ->with([
                'vendor:id,name,email,phone,created_at',
                'items:id,order_id,product_id,quantity,unit_price,subtotal',
                'items.product:id,name,price,stock',
            ])
            ->latest()
            ->paginate($this->perPage($request));

        $data = $orders->getCollection()->transform(fn (Order $order) => $this->orderToArray($order));

        return response()->json([
            'ok' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load([
            'vendor:id,name,email,phone,created_at',
            'items:id,order_id,product_id,quantity,unit_price,subtotal',
            'items.product:id,name,price,stock',
        ]);

        return response()->json([
            'ok' => true,
            'data' => $this->orderToArray($order),
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $order = DB::transaction(function () use ($validated) {
                $order = Order::create([
                    'vendor_id' => $validated['vendor_id'],
                    'status' => 'pending',
                    'total' => 0,
                    // Basic XSS prevention for free-text inputs.
                    'customer_name' => $this->sanitizeText($validated['customer_name'] ?? null),
                    'customer_phone' => $validated['customer_phone'] ?? null,
                ]);

                $total = 0;

                foreach ($validated['items'] as $item) {
                    $quantity = (int) $item['quantity'];

                    $product = Product::query()
                        ->lockForUpdate()
                        ->find($item['product_id']);

                    if (!$product) {
                        throw new \RuntimeException('Producto no encontrado.');
                    }

                    if ($product->stock < $quantity) {
                        throw new \RuntimeException('Stock insuficiente para el producto ID ' . $product->id . '.');
                    }

                    $unitPrice = (float) $product->price;
                    $subtotal = round($unitPrice * $quantity, 2);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $quantity);
                    $total += $subtotal;
                }

                $order->update(['total' => $total]);

                return $order->load([
                    'vendor:id,name,email,phone,created_at',
                    'items:id,order_id,product_id,quantity,unit_price,subtotal',
                    'items.product:id,name,price,stock',
                ]);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'data' => $this->orderToArray($order),
        ], 201);
    }

    private function orderToArray(Order $order): array
    {
        return [
            'id' => $order->id,
            'vendor' => $order->vendor ? [
                'id' => $order->vendor->id,
                'name' => $order->vendor->name,
                'email' => $order->vendor->email,
                'phone' => $order->vendor->phone,
            ] : null,
            'status' => $order->status,
            'total' => (string) $order->total,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'items' => $order->items->map(function (OrderItem $item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => (string) $item->product->price,
                        'stock' => $item->product->stock,
                    ] : null,
                    'quantity' => $item->quantity,
                    'unit_price' => (string) $item->unit_price,
                    'subtotal' => (string) $item->subtotal,
                ];
            })->values(),
        ];
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
