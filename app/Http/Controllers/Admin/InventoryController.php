<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filter === 'low_stock') {
            $query->whereRaw('stock_quantity <= low_stock_threshold');
        }

        $products = $query->paginate(20);
        return view('admin.inventory.index', compact('products'));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
            'note'       => 'nullable|string',
        ]);

        $this->inventoryService->stockIn(
            $request->product_id, $request->variant_id,
            $request->quantity, $request->note, auth()->id()
        );

        return back()->with('success', 'Stock added.');
    }

    public function stockOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
            'note'       => 'nullable|string',
        ]);

        $this->inventoryService->stockOut(
            $request->product_id, $request->variant_id,
            $request->quantity, $request->note, auth()->id()
        );

        return back()->with('success', 'Stock deducted.');
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:0',
            'note'       => 'nullable|string',
        ]);

        $this->inventoryService->adjust(
            $request->product_id, $request->variant_id,
            $request->quantity, $request->note, auth()->id()
        );

        return back()->with('success', 'Stock adjusted.');
    }

    public function history(Product $product)
    {
        $histories = $product->stockHistories()->with('creator', 'variant')->latest()->paginate(20);
        return view('admin.inventory.history', compact('product', 'histories'));
    }
}
