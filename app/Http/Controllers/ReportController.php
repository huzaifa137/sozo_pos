<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dashboard()
    {
        // Today's stats
        $today = today();
        $todaySales = Sale::whereDate('created_at', $today)->where('status', 'completed');
        $todayRevenue = (clone $todaySales)->sum('total');
        $todayCount = (clone $todaySales)->count();
        $todayProfit = (clone $todaySales)->with('items.inventoryItem')->get()
            ->flatMap->items
            ->sum(fn($i) => ($i->unit_price - ($i->inventoryItem?->buying_price ?? 0)) * $i->quantity);

        // This month
        $monthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed');
        $monthRevenue = (clone $monthSales)->sum('total');
        $monthCount = (clone $monthSales)->count();

        // Low stock
        $lowStock = InventoryItem::whereColumn('quantity', '<=', 'low_stock_threshold')
            ->where('quantity', '>', 0)->count();
        $outOfStock = InventoryItem::where('quantity', 0)->count();

        // Sales last 7 days (for chart)
        $salesChart = Sale::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling items this month
        $topItems = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereMonth('sales.created_at', now()->month)
            ->where('sales.status', 'completed')
            ->selectRaw('item_name, SUM(quantity) as total_qty, SUM(line_total) as total_revenue')
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Recent sales
        $recentSales = Sale::with('user', 'customer')
            ->where('status', 'completed')
            ->latest()->limit(5)->get();

        // Cashier performance today
        $cashierPerf = Sale::whereDate('sales.created_at', $today)
            ->where('sales.status', 'completed')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->selectRaw('users.name, COUNT(sales.id) as sales_count, SUM(sales.total) as total_revenue')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('reports.dashboard', compact(
            'todayRevenue',
            'todayCount',
            'todayProfit',
            'monthRevenue',
            'monthCount',
            'lowStock',
            'outOfStock',
            'salesChart',
            'topItems',
            'recentSales',
            'cashierPerf'
        ));
    }

    public function sales(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $sales = Sale::with('user', 'customer', 'items')
            ->whereBetween('created_at', [$from, $to])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->payment_method, fn($q) => $q->where('payment_method', $request->payment_method))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $totals = Sale::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('SUM(total) as revenue, SUM(tax_total) as tax, SUM(discount_amount) as discounts, COUNT(*) as count')
            ->first();

        return view('reports.sales', compact('sales', 'totals', 'from', 'to'));
    }

    public function inventory(Request $request)
    {
        $items = InventoryItem::when($request->filter === 'low', fn($q) => $q->whereColumn('quantity', '<=', 'low_stock_threshold'))
            ->when($request->filter === 'out', fn($q) => $q->where('quantity', 0))
            ->when($request->filter === 'expiring', fn($q) => $q->whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays(30)))
            ->orderBy('quantity')
            ->paginate(20)->withQueryString();

        $summary = [
            'total' => InventoryItem::count(),
            'low' => InventoryItem::whereColumn('quantity', '<=', 'low_stock_threshold')->where('quantity', '>', 0)->count(),
            'out' => InventoryItem::where('quantity', 0)->count(),
            'expiring' => InventoryItem::whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays(30))->count(),
        ];

        return view('reports.inventory', compact('items', 'summary'));
    }
}
