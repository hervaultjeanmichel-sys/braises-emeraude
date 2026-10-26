@extends('layouts.front')
@php($stores = \App\Models\Store::published()->orderBy('sort')->get())
@section('content')
    @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro,
        'actions' => '<a class="btn btn-main" href="/devis/" data-hover>'.e(s('contact.quote_cta', 'Je veux un devis')).'</a>'])
    <section class="content">
        <div class="wrap two-col">
            <form class="card form-grid" method="post" action="/contact/" novalidate>
                @csrf
                <h2>{!! em(s('contact.form_title', 'Une question ? *Écrivez-nous*')) !!}</h2>
                @if($errors->any())<div class="errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="form-grid cols-2">
                    <div class="field"><label for="c-name">{{ s('form.name', 'Nom et prénom') }} *</label><input id="c-name" type="text" name="name" required autocomplete="name" value="{{ old('name') }}"></div>
                    <div class="field"><label for="c-email">{{ s('form.email', 'E-mail') }} *</label><input id="c-email" type="email" name="email" required autocomplete="email" value="{{ old('email') }}"></div>
                    <div class="field"><label for="c-phone">{{ s('form.phone', 'Téléphone') }}</label><input id="c-phone" type="tel" name="phone" autocomplete="tel" value="{{ old('phone') }}"></div>
                    <div class="field"><label for="c-cp">{{ s('form.postal_code', 'Code postal') }}</label><input id="c-cp" type="text" name="postal_code" inputmode="numeric" maxlength="5" value="{{ old('postal_code') }}"></div>
                </div>
                <div class="field"><label for="c-store">{{ s('form.store', 'Magasin') }}</label>
                    <select id="c-store" name="store_id"><option value="">{{ s('form.store_auto', 'Le plus proche de chez moi') }}</option>@foreach($stores as $st)<option value="{{ $st->id }}" @selected((int) old('store_id') === $st->id)>{{ $st->area_label ?: $st->short_name }}</option>@endforeach</select></div>
                <div class="field"><label for="c-msg">{{ s('form.message_required', 'Votre message') }} *</label><textarea id="c-msg" name="message" rows="5" required>{{ old('message') }}</textarea></div>
                <label class="consent"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))><span>{{ s('form.consent', 'J\'accepte que Braises Émeraude utilise ces informations pour me recontacter au sujet de mon projet. Elles ne sont jamais transmises à des tiers.') }}</span></label>
                <button class="btn btn-main" type="submit" data-hover>{{ s('contact.submit', 'Envoyer') }}</button>
            </form>
            <div class="prose-page">{!! $page->content !!}</div>
        </div>
    </section>
    <section class="sec groupe bg-emerald" id="magasins"><div class="wrap">
        <div class="stores">@foreach($stores as $store)@include('front.partials.store-card', ['store' => $store, 'num' => $loop->iteration])@endforeach</div>
        <div class="groupe-map">@include('front.partials.store-map', ['mapStores' => $stores])</div>
    </div></section>
@endsection
