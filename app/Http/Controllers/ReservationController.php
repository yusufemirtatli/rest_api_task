<?php

namespace App\Http\Controllers;

use App\Enums\SeatStatus;
use App\Models\Events;
use App\Models\Reservation_items;
use App\Models\Reservations;
use App\Models\Seats;
use App\Models\Tickects;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservations::all();
        return response()->json($reservations, 200);
    }
    public function postIndex(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        $seatIds = $validated['seat_ids'];

        // Koltukların müsaitlik kontrolü
        $unavailableSeats = Seats::whereIn('id', $seatIds)
            ->where('status', SeatStatus::TAKEN->value)
            ->get();

        if ($unavailableSeats->isNotEmpty()) {
            return response()->json([
                'error' => 'Bazı koltuklar zaten rezerve edilmiş.',
                'unavailable_seats' => $unavailableSeats,
            ], 400);
        }

        // Rezervasyon oluşturma
        $reservation = Reservations::create([
            'user_id' => auth()->id(),
            'event_id' => $validated['event_id'],
            'status' => 'pending',
            'total_amount' => 0, // Şimdilik 0, sonradan hesaplanacak
            'expires_at' => now()->addMinutes(15),
        ]);

        // Rezervasyon öğelerini ekleme ve toplam tutarı hesaplama
        $totalAmount = 0;

        foreach ($seatIds as $seatId) {
            $seat = Seats::find($seatId);

            // Koltuğun fiyatını reservation_items tablosuna ekliyoruz
            Reservation_items::create([
                'reservation_id' => $reservation->id,
                'seat_id' => $seatId,
                'price' => $seat->price,
            ]);

            // Toplam tutarı güncelleme
            $totalAmount += $seat->price;

            // Koltuk durumunu güncelleme
            $seat->update(['status' => SeatStatus::TAKEN->value]);
        }

        // Toplam tutarı rezervasyona kaydetme
        $reservation->update(['total_amount' => $totalAmount]);

        return response()->json([
            'message' => 'Rezervasyon başarıyla oluşturuldu.',
            'reservation' => $reservation,
            'items' => $reservation->items,
        ], 201);
    }

    public function expireReservations()
    {
        $expiredReservations = Reservations::where('expires_at', '<', now())
            ->where('status', 'pending')
            ->get();

        foreach ($expiredReservations as $reservation) {
            // Koltukları serbest bırakma
            foreach ($reservation->items as $item) {
                $seat = Seats::find($item->seat_id);
                if ($seat) {
                    $seat->update(['status' => SeatStatus::EMPTY->value]);
                }
            }

            // Rezervasyon ve öğelerini silme
            $reservation->items()->delete();
            $reservation->delete();
        }

        return response()->json(['message' => 'Süresi dolan rezervasyonlar iptal edildi.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reservation = Reservations::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('items.seat') // İlgili reservation_items ve seat bilgilerini dahil et
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Rezervasyon bulunamadı.'], 404);
        }

        return response()->json([
            'message' => 'Rezervasyon detayı başarıyla getirildi.',
            'reservation' => $reservation,
        ]);
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
    public function destroy($id)
    {
        $reservation = Reservations::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Rezervasyon bulunamadı.'], 404);
        }

        // Etkinlik bilgilerini al
        $event = Events::find($reservation->event_id);

        // Etkinliğin başlama zamanından 24 saat öncesini al
        $startTime = Carbon::parse($event->start_date);
        $twentyFourHoursBefore = $startTime->subHours(24);

        // Eğer şu anki zaman, etkinlik başlamadan 24 saat öncesinden az ise iptal edilemez
        if (Carbon::now()->greaterThanOrEqualTo($twentyFourHoursBefore)) {
            return response()->json(['error' => 'Etkinliğin başlamasına 24 saatten az bir süre kaldığı için iptal işlemi yapılamaz.'], 400);
        }

        // Koltukların durumunu güncelleme
        foreach ($reservation->items as $item) {
            $seat = Seats::find($item->seat_id);
            if ($seat) {
                $seat->update(['status' => SeatStatus::EMPTY->value]);
            }
        }

        // Rezervasyona ait tüm ticketları silme
        $tickets = Tickects::where('reservation_id', $reservation->id)->get();
        foreach ($tickets as $ticket) {
            $ticket->delete();
        }

        // Rezervasyon ve bağlı öğeleri silme
        $reservation->items()->delete();
        $reservation->delete();

        return response()->json(['message' => 'Rezervasyon ve bağlı biletler başarıyla iptal edildi.']);
    }

    public function confirm($id)
    {
        $reservation = Reservations::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Onaylanabilir bir rezervasyon bulunamadı.'], 404);
        }

        // Rezervasyonu onaylama
        $reservation->update(['status' => 'confirmed']);

        $r_items = Reservation_items::where('reservation_id',$reservation->id)->get();
        // Rezervasyona ait her item için bilet oluştur
        foreach ($r_items as $item) {
            // Koltuğu al
            $seat = Seats::find($item->seat_id);

            if ($seat) {
                // Bilet oluştur
                $ticket = new Tickects();
                $ticket->reservation_id = $reservation->id;
                $ticket->seat_id = $seat->id;
                $ticket->ticket_code = mt_rand(1000000000, 9999999999);  // Benzersiz ticket_code (10 haneli)
                $ticket->status = 'unused';  // Başlangıçta kullanılmamış
                $ticket->save();
            }
        }

        return response()->json([
            'message' => 'Rezervasyon başarıyla onaylandı.',
            'reservation' => $reservation,
        ]);
    }
}
