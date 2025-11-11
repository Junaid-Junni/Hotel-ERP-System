<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTable();
        }

        $categories = Inventory::distinct()->pluck('category');
        $stockSummary = $this->getStockSummary();

        return view('inventory.index', compact('categories', 'stockSummary'));
    }

    public function getDataTable(Request $request)
    {
        $query = Inventory::query();

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('stock_status') && !empty($request->stock_status)) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->whereRaw('quantity <= min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'in_stock':
                    $query->where('quantity', '>', 0);
                    break;
            }
        }

        return DataTables::of($query)
            ->addIndexColumn() // Add this line for automatic index
            ->addColumn('action', function ($row) {
                // Use $row and pass it to the view
                return view('inventory.dt_buttons', compact('row'))->render();
            })
            ->editColumn('cost_price', function ($row) {
                return '$' . number_format($row->cost_price, 2);
            })
            ->editColumn('selling_price', function ($row) {
                return '$' . number_format($row->selling_price, 2);
            })
            ->editColumn('quantity', function ($row) {
                $html = $row->quantity;
                if ($row->min_stock_level > 0) {
                    $html .= '<br><small class="text-muted">Min: ' . $row->min_stock_level . '</small>';
                }
                return $html;
            })
            ->addColumn('stock_status', function ($row) {
                if ($row->quantity <= 0) {
                    return '<span class="badge bg-danger">Out of Stock</span>';
                } elseif ($row->quantity <= $row->min_stock_level) {
                    return '<span class="badge bg-warning">Low Stock</span>';
                } else {
                    return '<span class="badge bg-success">In Stock</span>';
                }
            })
            ->editColumn('status', function ($row) {
                $badgeClass = [
                    'active' => 'bg-success',
                    'inactive' => 'bg-secondary',
                    'discontinued' => 'bg-dark'
                ][$row->status] ?? 'bg-secondary';

                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('total_value', function ($row) {
                $totalValue = $row->cost_price * $row->quantity;
                return '$' . number_format($totalValue, 2);
            })
            ->rawColumns(['action', 'quantity', 'stock_status', 'status'])
            ->make(true);
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'sku' => 'required|unique:inventory,sku|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'category' => 'required|max:100',
            'brand' => 'nullable|max:100',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'location' => 'nullable|max:100',
            'supplier' => 'nullable|max:100',
            'supplier_contact' => 'nullable|max:100',
            'expiry_date' => 'nullable|date',
            'barcode' => 'nullable|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        DB::beginTransaction();
        try {
            $inventoryData = $request->all();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('inventory', 'public');
                $inventoryData['image'] = $imagePath;
            }

            $inventoryData['created_by'] = auth()->id();
            $inventoryData['updated_by'] = auth()->id();

            Inventory::create($inventoryData);

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory item created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create inventory item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $inventory = Inventory::findOrFail($id);
        return view('inventory.show', compact('inventory'));
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validator = $request->validate([
            'sku' => 'required|unique:inventory,sku,' . $inventory->id . '|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'category' => 'required|max:100',
            'brand' => 'nullable|max:100',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'location' => 'nullable|max:100',
            'supplier' => 'nullable|max:100',
            'supplier_contact' => 'nullable|max:100',
            'expiry_date' => 'nullable|date',
            'barcode' => 'nullable|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        DB::beginTransaction();
        try {
            $inventoryData = $request->all();

            if ($request->hasFile('image')) {
                // Delete old image
                if ($inventory->image) {
                    Storage::disk('public')->delete($inventory->image);
                }
                $imagePath = $request->file('image')->store('inventory', 'public');
                $inventoryData['image'] = $imagePath;
            }

            $inventoryData['updated_by'] = auth()->id();

            $inventory->update($inventoryData);

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory item updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update inventory item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($id);

            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }

            $inventory->delete();

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory item moved to trash successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete inventory item: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $trashedItems = Inventory::onlyTrashed()->get();
        return view('inventory.trash', compact('trashedItems'));
    }

    public function restore($id)
    {
        try {
            $inventory = Inventory::onlyTrashed()->findOrFail($id);
            $inventory->restore();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory item restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore inventory item: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $inventory = Inventory::onlyTrashed()->findOrFail($id);

            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }

            $inventory->forceDelete();

            return back()->with('success', 'Inventory item permanently deleted!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete inventory item: ' . $e->getMessage());
        }
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'adjustment_type' => 'required|in:set,add,subtract',
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($id);
            $oldQuantity = $inventory->quantity;

            switch ($request->adjustment_type) {
                case 'set':
                    $newQuantity = $request->quantity;
                    break;
                case 'add':
                    $newQuantity = $oldQuantity + $request->quantity;
                    break;
                case 'subtract':
                    $newQuantity = $oldQuantity - $request->quantity;
                    if ($newQuantity < 0) $newQuantity = 0;
                    break;
            }

            $inventory->update([
                'quantity' => $newQuantity,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'new_quantity' => $newQuantity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function lowStock()
    {
        $lowStockItems = Inventory::lowStock()->get();
        return view('inventory.low-stock', compact('lowStockItems'));
    }

    private function getStockSummary()
    {
        return [
            'total_items' => Inventory::count(),
            'total_value' => Inventory::sum(DB::raw('cost_price * quantity')),
            'low_stock_items' => Inventory::lowStock()->count(),
            'out_of_stock_items' => Inventory::outOfStock()->count(),
            'active_items' => Inventory::active()->count()
        ];
    }
}
