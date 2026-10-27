<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Store;
use App\Support\Booking;
use App\Support\BookingMailer;
use App\Support\Seo;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Prise de rendez-vous en ligne : /rendez-vous/ (formulaire), /rendez-vous/creneaux (JSON), envoi, /rendez-vous/merci/.
 * Chaque demande reste « en attente » jusqu'à validation par l'équipe du magasin (admin › Rendez-vous).
 */
class AppointmentController extends Controller
{
    private function stores()
    {
        return Store::published()->where('booking_enabled', true)->orderBy('sort')->get()
            ->filter(fn (Store $s) => Booking::for($s)->isEnabled())->values();
    }

    private function seo(string $title, string $url): Seo
    {
        $seo = new Seo;
        $seo->title = $title;
        $seo->description = s('booking.seo_description', 'Prenez rendez-vous en ligne avec Braises Émeraude : conseil au showroom, visite technique à domicile ou ramonage. Confirmation par notre équipe.');
        $seo->canonical = $url;
        $seo->robots = 'noindex,follow';
        $seo->breadcrumb = [['label' => $title, 'url' => $url]];

        return $seo;
    }

    public function show(Request $request): View
    {
        $stores = $this->stores();
        $pre = $stores->firstWhere('id', $request->integer('magasin'));

        return view('front.booking', [
            'seo' => $this->seo(s('booking.title', 'Prendre rendez-vous'), '/rendez-vous/'),
            'stores' => $stores,
            'preStore' => $pre ?? ($stores->count() === 1 ? $stores->first() : null),
            'typesByStore' => $stores->mapWithKeys(fn ($s) => [$s->id => array_values(Booking::for($s)->types())]),
        ]);
    }

    /** ?magasin=&type= → jours disponibles ; + &date= → créneaux du jour. */
    public function slots(Request $request): JsonResponse
    {
        $store = $this->stores()->firstWhere('id', $request->integer('magasin'));
        $type = (string) $request->query('type');
        if (! $store || ! isset(Booking::for($store)->types()[$type])) {
            return response()->json(['error' => 'unknown'], 404);
        }
        $booking = Booking::for($store);
        if ($date = $request->query('date')) {
            return response()->json(['date' => $date, 'slots' => $booking->slots($type, (string) $date)]);
        }

        return response()->json(['days' => $booking->days($type)]);
    }

    /** ?magasin=&cp=&ville= → zone d'intervention à domicile (contrôle dès la saisie du code postal). */
    public function zone(Request $request): JsonResponse
    {
        $store = $this->stores()->firstWhere('id', $request->integer('magasin'));
        $cp = (string) $request->query('cp');
        if (! $store || ! preg_match('/^\d{5}$/', $cp)) {
            return response()->json(['ok' => null]);
        }

        return response()->json(Booking::for($store)->zone($cp, (string) $request->query('ville')));
    }

    public function store(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) { // pot de miel
            return new RedirectResponse('/rendez-vous/merci/');
        }
        $data = $request->validate([
            'store_id' => ['required', 'integer'],
            'type' => ['required', 'string', 'max:60'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:190'],
            'postal_code' => ['nullable', 'regex:/^\d{5}$/'],
            'city' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ], [], ['name' => 'nom', 'phone' => 'téléphone', 'email' => 'e-mail', 'postal_code' => 'code postal', 'address' => 'adresse', 'city' => 'commune', 'time' => 'créneau', 'date' => 'date', 'consent' => 'consentement']);

        $store = $this->stores()->firstWhere('id', (int) $data['store_id']);
        $booking = $store ? Booking::for($store) : null;
        $type = $booking?->types()[$data['type']] ?? null;
        if (! $store || ! $type) {
            throw ValidationException::withMessages(['type' => s('booking.err_type', 'Ce type de rendez-vous n’est pas disponible.')]);
        }
        if ($type['at_home']) {
            $request->validate(['address' => ['required'], 'postal_code' => ['required'], 'city' => ['required']], [], ['address' => 'adresse', 'postal_code' => 'code postal', 'city' => 'commune']);
            $zone = $booking->zone($data['postal_code'] ?? null, $data['city'] ?? null, $data['address'] ?? null);
            if ($zone['ok'] === false) {
                throw ValidationException::withMessages(['postal_code' => str_replace([':km', ':radius'], [(string) round((float) $zone['km']), (string) $zone['radius']],
                    s('booking.err_zone', 'Votre adresse est à environ :km km du magasin, au-delà de sa zone d’intervention (:radius km). Choisissez un autre magasin ou appelez-nous.'))]);
            }
        }
        if (! $booking->isFree($data['type'], $data['date'], $data['time'])) {
            throw ValidationException::withMessages(['time' => s('booking.err_slot', 'Ce créneau vient d’être pris. Merci d’en choisir un autre.')]);
        }

        $start = CarbonImmutable::parse($data['date'].' '.$data['time'], Booking::TZ);
        $appointment = Appointment::create([
            'store_id' => $store->id,
            'type_key' => $type['key'],
            'type_label' => $type['label'],
            'at_home' => $type['at_home'],
            'starts_at' => $start,
            'ends_at' => $start->addMinutes($type['duration']),
            'status' => 'en_attente',
            'name' => $data['name'], 'phone' => $data['phone'], 'email' => $data['email'],
            'address' => $type['at_home'] ? $data['address'] : null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $type['at_home'] ? $data['city'] : null,
            'message' => $data['message'] ?? null,
            'team_note' => ($type['at_home'] && isset($zone)) ? ($zone['ok'] === null ? 'Distance non vérifiée (adresse non localisée).' : ($zone['km'] !== null ? 'Distance estimée : '.$zone['km'].' km à vol d’oiseau.' : null)) : null,
            'source' => mb_substr((string) (session('utm')['utm_source'] ?? 'site'), 0, 40),
            'consent_at' => now(),
            'ip_hash' => hash('sha256', (string) $request->ip().config('app.key')),
        ]);

        BookingMailer::received($appointment);

        return (new RedirectResponse('/rendez-vous/merci/'))->with('appointment_id', $appointment->id);
    }

    public function thanks(Request $request): View
    {
        $appointment = ($id = session('appointment_id')) ? Appointment::with('store')->find($id) : null;

        return view('front.booking-thanks', ['seo' => $this->seo(s('booking.thanks_title', 'Demande de rendez-vous envoyée'), '/rendez-vous/merci/'), 'appointment' => $appointment]);
    }
}
