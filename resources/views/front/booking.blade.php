{{-- Prise de rendez-vous : magasin → type → jour + créneau → coordonnées. Créneaux calculés par App\Support\Booking. --}}
@extends('layouts.front')
@php($oldBooking = ['store_id' => old('store_id'), 'type' => old('type'), 'date' => old('date'), 'time' => old('time')])
@section('content')
    @include('front.partials.hero', ['eyebrow' => s('booking.eyebrow', 'Rendez-vous'), 'h1' => s('booking.h1', 'Prendre rendez-vous'),
        'intro' => s('booking.intro', 'Choisissez votre magasin, le type de rendez-vous et un créneau. Notre équipe confirme votre rendez-vous par e-mail ou par téléphone.')])

    <section class="content">
        <div class="wrap">
            @if($stores->isEmpty())
                <p class="lead">{{ s('booking.none', 'La prise de rendez-vous en ligne n’est pas encore ouverte. Appelez votre magasin ou demandez un devis gratuit.') }}</p>
                <p><a class="btn btn-main" href="/devis/" data-hover>{{ s('cta.quote', 'Devis gratuit') }}</a></p>
            @else
            <form class="card form-grid booking" method="post" action="/rendez-vous/" novalidate data-booking
                  data-types='@json($typesByStore)' data-slots-url="/rendez-vous/creneaux"
                  data-old='@json($oldBooking)'>
                @csrf
                <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                @if($errors->any())
                    <div class="errors" role="alert"><strong>{{ s('form.errors', 'Merci de vérifier les champs suivants :') }}</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <noscript><p class="errors">{{ s('booking.nojs', 'Le choix des créneaux nécessite JavaScript. Vous pouvez aussi nous appeler.') }}</p></noscript>

                <fieldset class="bk-step">
                    <legend><span>1</span> {{ s('booking.step_store', 'Votre magasin') }}</legend>
                    <div class="bk-choices">
                        @foreach($stores as $st)
                            <label class="bk-choice">
                                <input type="radio" name="store_id" value="{{ $st->id }}" @checked((int) old('store_id', $preStore?->id) === $st->id) required>
                                <span><strong>{{ $st->area_label ?: $st->short_name }}</strong><small>{{ $st->addressLine() }}</small></span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="bk-step" data-step="type" hidden>
                    <legend><span>2</span> {{ s('booking.step_type', 'Le rendez-vous') }}</legend>
                    <div class="bk-choices" data-types-list></div>
                </fieldset>

                <fieldset class="bk-step" data-step="when" hidden>
                    <legend><span>3</span> {{ s('booking.step_when', 'Le jour et l’heure') }}</legend>
                    <div class="bk-days" data-days role="listbox" aria-label="{{ s('booking.days_aria', 'Jours disponibles') }}"></div>
                    <div class="bk-slots" data-slots aria-live="polite"></div>
                    <p class="hint" data-when-empty hidden>{{ s('booking.no_slot', 'Aucun créneau disponible pour le moment : appelez-nous, nous trouverons une solution.') }}</p>
                    <input type="hidden" name="date" value="{{ old('date') }}">
                    <input type="hidden" name="time" value="{{ old('time') }}">
                </fieldset>

                <fieldset class="bk-step" data-step="contact" hidden>
                    <legend><span>4</span> {{ s('booking.step_contact', 'Vos coordonnées') }}</legend>
                    <p class="bk-summary" data-summary></p>
                    <div class="form-grid cols-2">
                        <div class="field"><label for="b-name">{{ s('form.name', 'Nom et prénom') }} *</label><input id="b-name" type="text" name="name" required autocomplete="name" value="{{ old('name') }}"></div>
                        <div class="field"><label for="b-phone">{{ s('form.phone', 'Téléphone') }} *</label><input id="b-phone" type="tel" name="phone" required autocomplete="tel" value="{{ old('phone') }}"></div>
                        <div class="field"><label for="b-email">{{ s('form.email', 'E-mail') }} *</label><input id="b-email" type="email" name="email" required autocomplete="email" value="{{ old('email') }}"></div>
                        <div class="field"><label for="b-cp">{{ s('form.postal_code', 'Code postal') }} <span data-home-only hidden>*</span></label><input id="b-cp" type="text" name="postal_code" inputmode="numeric" maxlength="5" autocomplete="postal-code" value="{{ old('postal_code') }}"></div>
                        <div class="field" data-home-only hidden><label for="b-address">{{ s('booking.address', 'Adresse du chantier') }} *</label><input id="b-address" type="text" name="address" autocomplete="street-address" value="{{ old('address') }}"></div>
                        <div class="field" data-home-only hidden><label for="b-city">{{ s('booking.city', 'Commune') }} *</label><input id="b-city" type="text" name="city" autocomplete="address-level2" value="{{ old('city') }}"></div>
                    </div>
                    <div class="field"><label for="b-msg">{{ s('booking.message', 'Votre projet en quelques mots (facultatif)') }}</label><textarea id="b-msg" name="message" rows="3">{{ old('message') }}</textarea></div>
                    <label class="consent"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))><span>{{ s('booking.consent', 'J’accepte que Braises Émeraude utilise ces informations pour organiser ce rendez-vous. Elles ne sont jamais transmises à des tiers.') }}</span></label>
                    <p class="hint">{{ s('booking.pending_note', 'Votre rendez-vous sera confirmé par notre équipe : vous recevez un e-mail dès sa validation.') }}</p>
                    <button class="btn btn-main" type="submit" data-hover>{{ s('booking.submit', 'Demander ce rendez-vous') }}</button>
                </fieldset>
            </form>
            @push('scripts')<script src="/js/booking.js?v={{ config('braises.asset_version') }}" defer></script>@endpush
            @endif
        </div>
    </section>
@endsection
