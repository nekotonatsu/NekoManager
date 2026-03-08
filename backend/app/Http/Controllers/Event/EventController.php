<?php

namespace App\Http\Event\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->events();

        if ($request->has('month')) {
            $query->whereYear('start_date', substr($request->month, 0, 4))
                ->whereMonth('start_date', substr($request->month, 5, 2));
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