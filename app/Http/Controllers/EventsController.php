<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Events::all();
        return response()->json($events, 200);
    }
    public function getEvent($id)
    {
        $event = Events::find($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json($event, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            // Kullanıcı giriş yapmamış, yönlendirme veya hata işlemleri yapılabilir
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Kullanıcının role_id kontrolü
        if (auth()->user()->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized. Only admins can create events.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000', // Description için 1000 karakter limiti
            'venue_id' => 'required|integer|exists:venues,id', // venues tablosunda id doğrulaması
            'start_date' => 'required|date|after:today', // Start_date bugünden sonra olmalı
            'end_date' => 'required|date|after_or_equal:start_date', // End_date start_date'e eşit ya da daha sonra olmalı
        ]);


        $event = Events::create($validated);

        return response()->json($event, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->check()) {
            // Kullanıcı giriş yapmamış, yönlendirme veya hata işlemleri yapılabilir
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Kullanıcının role_id kontrolü
        if (auth()->user()->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized. Only admins can update events.'], 403);
        }

        $event = Events::find($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000', // Description için 1000 karakter limiti
            'venue_id' => 'required|integer|exists:venues,id', // venues tablosunda id doğrulaması
            'start_date' => 'required|date|after:today', // Start_date bugünden sonra olmalı
            'end_date' => 'required|date|after_or_equal:start_date', // End_date start_date'e eşit ya da daha sonra olmalı
        ]);

        $event->update($validated);

        return response()->json($event, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->check()) {
            // Kullanıcı giriş yapmamış, yönlendirme veya hata işlemleri yapılabilir
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Kullanıcının role_id kontrolü
        if (auth()->user()->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized. Only admins can delete events.'], 403);
        }

        $event = Events::find($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found.'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
