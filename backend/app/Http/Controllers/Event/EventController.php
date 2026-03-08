<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!($request->has('year') && $request->has('month'))) {
            // ここは後ほどrequestがない等エラーに変更する
            return null;
        }

        $validated = $request->validate([
            'month' => 'sometimes|date_format:m',
            'year' => 'sometimes|date_format:Y'
        ]);
        if (empty($validated['year'] || empty($validated['month']))) {
            // ここは後ほどvalidate後に値が含まれていない等エラーに変更する
            return null;
        }

        $query = Auth::user()->events();

        if ($request->has('month') && !empty($validated['month'])) {
            $query->whereYear('start_date', $validated['year'])
                ->whereMonth('start_date', $validated['month']);
        }

        return response()->json($query->orderBy('start_date')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $event = Auth::user()->events()->create($validated);
        return response()->json($event, 201);
    }

    public function show(Event $event): JsonResponse
    {
        $this->authorize('view', $event);
        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'sometimes|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $event->update($validated);
        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);
        $event->delete();
        return response()->json(null, 204);
    }
}