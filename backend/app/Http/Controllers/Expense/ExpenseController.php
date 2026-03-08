<?php

namespace App\Http\Expense\Controllers;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'sometimes|date_format:Y-m',
        ]);
        $query = Auth::user()->expenses();

        if ($request->has('month') && isset($validated['month'])) {
            $month = Carbon::createFromFormat('Y-m', $validated['month']);
            $query->whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month);
        }

        return response()->json($query->orderBy('expense_date')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'amount'   => 'required|integer|min:0',
            'expense_date'     => 'required|date',
            'category' => 'nullable|string|max:255',
        ]);

        $expense = Auth::user()->expenses()->create($validated);
        return response()->json($expense, 201);
    }

    public function show(Expense $expense): JsonResponse
    {
        $this->authorize('view', $expense);
        return response()->json($expense);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'title'    => 'sometimes|string|max:255',
            'amount'   => 'sometimes|integer|min:0',
            'expense_date'     => 'sometimes|expense_date',
            'category' => 'nullable|string|max:255',
        ]);

        $expense->update($validated);
        return response()->json($expense);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorize('delete', $expense);
        $expense->delete();
        return response()->json(null, 204);
    }

    public function summary(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);

        // 月次サマリを DB ネイティブ集計で取得（例: "2025-01" => 12345）
        $summary = Auth::user()->expenses()
            ->whereYear('date', $year)
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // 年間合計も DB 上で集計
        $total = Auth::user()->expenses()
            ->whereYear('date', $year)
            ->sum('amount');
        return response()->json([
            'year'    => (int) $year,
            'summary' => $summary,
            'total'   => $total,
        ]);
    }
}