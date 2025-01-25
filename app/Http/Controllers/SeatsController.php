<?php

namespace App\Http\Controllers;

use App\Enums\SeatStatus;
use App\Models\Events;
use App\Models\Seats;
use Illuminate\Http\Request;

class SeatsController extends Controller
{
    public function events($id)
    {
        // Event'i bul
        $event = Events::find($id);

        // Eğer event bulunmazsa, hata mesajı döndür
        if (!$event) {
            return response()->json([
                'error' => 'Event not found'
            ], 404); // 404 HTTP hatası döndürüyoruz (Not Found)
        }

        // Venue_id'yi alıyoruz
        $venue_id = $event->venue_id;

        // İlgili venue_id'ye sahip koltukları çekiyoruz
        $seats = Seats::where('venue_id', $venue_id)->get();

        // Koltukları JSON formatında döndürüyoruz
        return response()->json($seats);
    }

    public function venues($id){
        // venue_id'ye sahip olan tüm koltukları çekiyoruz
        $seats = Seats::where('venue_id', $id)->get();

        // Koltukları JSON formatında döndürüyoruz
        return response()->json($seats);
    }
    public function block(Request $request)
    {
        // Event ID ve Seat ID'yi alıyoruz
        $eventId = $request->input('event_id');
        $seatId = $request->input('seat_id');

        // Event'e ait venue_id'yi buluyoruz
        $event = Events::find($eventId);

        if (!$event) {
            return response()->json(['error' => 'Etkinlik bulunamadı'], 404);
        }

        // Etkinlikten venue_id'yi alıyoruz
        $venueId = $event->venue_id;

        // Belirtilen venue_id'ye sahip olan koltukları alıyoruz
        $seat = Seats::where('venue_id', $venueId)
            ->where('id', $seatId)
            ->first();

        if (!$seat) {
            return response()->json(['error' => 'Koltuk bulunamadı veya yanlış event_id'], 404);
        }

        // Koltuğun mevcut statüsünü kontrol ediyoruz
        if ($seat->status == SeatStatus::TAKEN->value) {
            return response()->json(['error' => 'Koltuk zaten rezerve edilmiş'], 400);
        }

        // Koltuğu blokluyoruz (empty -> reservation_process statüsüne getiriyoruz)
        $seat->status = SeatStatus::TAKEN;
        $seat->save();

        // Rezervasyon işlemi için zaman aşımını ayarlıyoruz (15 dakika sonra serbest bırakma işlemi için)
        $this->scheduleRelease($seat);

        return response()->json(['message' => 'Koltuk başarıyla bloklandı'], 200);
    }



    public function release(Request $request)
    {
        // Gerekli parametreleri alıyoruz
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id', // event_id doğrulaması
            'seat_id' => 'required|exists:seats,id', // seat_id doğrulaması
        ]);

        // Event ve seat ID'lerini alıyoruz
        $event_id = $validated['event_id'];
        $seat_id = $validated['seat_id'];

        // Etkinliği buluyoruz ve event'ın venue_id'sine göre koltukları kontrol ediyoruz
        $event = Events::find($event_id);

        if (!$event) {
            return response()->json(['error' => 'Etkinlik bulunamadı.'], 404);
        }

        // Venue ID'yi alıyoruz
        $venue_id = $event->venue_id;

        // Koltuklar arasından belirtilen seat_id'ye sahip koltuğu buluyoruz
        $seat = Seats::where('venue_id', $venue_id)
            ->where('id', $seat_id)
            ->whereIn('status', [SeatStatus::TAKEN, SeatStatus::EMPTY])
            ->first();

        // Koltuk bulunamazsa hata mesajı döndürüyoruz
        if (!$seat) {
            return response()->json(['error' => 'Koltuk bulunamadı veya zaten serbest bırakılmış.'], 404);
        }

        // Koltuğu serbest bırakıyoruz
        $seat->status = SeatStatus::EMPTY;
        $seat->save();

        // Başarı mesajı
        return response()->json(['message' => 'Koltuk başarıyla serbest bırakıldı.']);
    }

    public function scheduleRelease(Seats $seat)
    {
        $releaseTime = now()->addMinutes(15);
        $seat->status = SeatStatus::EMPTY;

        \Illuminate\Support\Facades\Log::info('Koltuk rezerve edildi, serbest bırakılacak zaman: ' . $releaseTime);

    }

}
