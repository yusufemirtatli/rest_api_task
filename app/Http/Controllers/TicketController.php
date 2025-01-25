<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\Reservations;
use App\Models\Seats;
use App\Models\Tickects;
use App\Models\User;
use App\Models\Venues;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Tickects::all();
        return response()->json($tickets, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Bilet ID'sine göre bilet bilgilerini al
    $ticket = Tickects::find($id);

    // Eğer bilet bulunamazsa hata döndür
    if (!$ticket) {
        return response()->json(['error' => 'Bilet bulunamadı.'], 404);
    }
    $seat = Seats::find($ticket->seat_id);
    $reservation = Reservations::find($ticket->reservation_id);
    $event = Events::find($reservation->event_id);
    $venue = Venues::find($event->venue_id);
    $user = User::find($reservation->user_id);

    // Biletin ilgili olduğu rezervasyon, koltuk bilgilerini dahil et
    $ticketDetails = [
        'ticket_id' => $ticket->id,
        'ticket_code' => $ticket->ticket_code,
        'status' => $ticket->status,
        'price' => $seat->price,
        'event_details' => [
            'name' => $event->name,
            'description' => $event->description,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
        ],
        'venue_details' => [
            'name' => $venue->name,
            'address' => $venue->address,
        ],
        'seat_details' => [
            'section' => $seat->section,
            'row' => $seat->row,
            'number' => $seat->number,
        ],
        'user_details' => [
            'name' => $user->name,
            'email' => $user->email,
        ],
    ];

    return response()->json($ticketDetails);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function download($id)
    {
        // Bilet ID'sine göre bilet bilgilerini al
        $ticket = Tickects::find($id);

        // Eğer bilet bulunamazsa hata döndür
        if (!$ticket) {
            return response()->json(['error' => 'Bilet bulunamadı.'], 404);
        }

        $seat = Seats::find($ticket->seat_id);
        $reservation = Reservations::find($ticket->reservation_id);
        $event = Events::find($reservation->event_id);
        $venue = Venues::find($event->venue_id);
        $user = User::find($reservation->user_id);

        // Biletin bilgilerini hazırlıyoruz
        $ticketDetails = [
            'ticket_id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'status' => $ticket->status,
            'price' => $seat->price,
            'event_details' => [
                'name' => $event->name,
                'description' => $event->description,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
            ],
            'venue_details' => [
                'name' => $venue->name,
                'address' => $venue->address,
            ],
            'seat_details' => [
                'section' => $seat->section,
                'row' => $seat->row,
                'number' => $seat->number,
            ],
            'user_details' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        // PDF'yi oluştur
        $pdf = PDF::loadView('pdf.ticket', $ticketDetails);

        // PDF'yi indirme olarak gönder
        return $pdf->download('bilet_' . $ticket->ticket_code . '.pdf');
    }
    public function transfer($id, Request $request)
    {
        // Biletin ID'si ile biletin bilgilerini al
        $ticket = Tickects::find($id);

        // Eğer bilet bulunamazsa hata döndür
        if (!$ticket) {
            return response()->json(['error' => 'Bilet bulunamadı.'], 404);
        }

        // Biletin geçerli olup olmadığını kontrol et
        if ($ticket->status !== 'unused') {
            return response()->json(['error' => 'Bu bilet transfer edilemez.'], 400);
        }

        // Transfer edilen kullanıcıyı al
        $newUserId = $request->input('user_id');

        // Kullanıcı var mı kontrol et
        $newUser = User::find($newUserId);
        if (!$newUser) {
            return response()->json(['error' => 'Geçersiz kullanıcı.'], 400);
        }

        // Biletin rezervasyonunu al
        $reservation = Reservations::find($ticket->reservation_id);
        if (!$reservation) {
            return response()->json(['error' => 'Rezervasyon bulunamadı.'], 404);
        }

        // Rezervasyonun kullanıcı bilgisini güncelle
        $reservation->user_id = $newUserId;
        $reservation->save();

        // Bilet kodunu yeniden oluştur
        $ticket->ticket_code = rand(100000, 999999);
        $ticket->save();

        return response()->json([
            'message' => 'Bilet başarıyla transfer edildi.',
            'ticket' => $ticket,
        ]);
    }


    public function expireTickets()
    {
        // Tüm etkinlikleri al
        $events = Events::all();

        foreach ($events as $event) {
            // Etkinliğin başlama zamanından 24 saat öncesini al
            $startTime = Carbon::parse($event->start_date);
            $twentyFourHoursBefore = $startTime->subHours(24);

            // Eğer şu anki zaman, etkinlik başlamadan 24 saat öncesi ise
            if (Carbon::now()->greaterThanOrEqualTo($twentyFourHoursBefore)) {
                $reservations = Reservations::where('event_id',$event->id)->get();
                foreach ($reservations as $rs){
                    // Etkinlik ile ilgili tüm biletlerin durumunu "used" yap
                    Tickects::where('reservation_id', $rs->reservation_id)
                        ->where('status', 'unused')
                        ->update(['status' => 'used']);
                    }
                }
        }
    }

}
