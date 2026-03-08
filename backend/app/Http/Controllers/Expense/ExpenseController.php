<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'expense_date'     => 'sometimes|date',
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
        $validated = $request->validate([
            'year' => 'sometimes|integer|digits:4|min:2000|max:2999',
        ]);
        $year = $validated['year'] ?? now()->year;

        // 対象年の支出を取得（アプリケーション層で月次サマリを集計）
        $expenses = Auth::user()->expenses()
            ->whereYear('expense_date', $year)
            ->orderBy('expense_date')
            ->get(['expense_date', 'amount']);

        // 月次サマリを PHP / Carbon 側で集計（例: "2025-01" => 12345）
        $summary = $expenses
            ->groupBy(function ($expense) {
                return Carbon::parse($expense->expense_date)->format('Y-m');
            })
            ->map(function ($group) {
                return $group->sum('amount');
            })
            ->sortKeys();

        // 年間合計も DB 上で集計
        $total = (int) $summary->sum();

        return response()->json([
            'year'    => (int) $year,
            'summary' => $summary,
            'total'   => $total,
        ]);
    }
}