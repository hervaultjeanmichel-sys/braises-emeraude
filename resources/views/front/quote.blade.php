{{-- Demande de devis en 8 étapes. Sans JavaScript, toutes les étapes s'affichent à la suite (formulaire complet). --}}
@extends('layouts.front')
@php($postcodes = $stores->mapWithKeys(fn ($st) => [$st->id => $st->served_postcodes ?? []]))
@php($steps = [
    1 => s('devis.step1', 'Quel est votre projet ?'),
    2 => s('devis.step2', 'Quel type d\'appareil ?'),
    3 => s('devis.step3', 'Quelle énergie souhaitez-vous ?'),
    4 => s('devis.step4', 'Que remplacez-vous ?'),
    5 => s('devis.step5', 'Parlez-nous de votre logement'),
    6 => s('devis.step6', 'Une marque vous fait envie ?'),
    7 => s('devis.step7', 'Votre budget et votre calendrier'),
    8 => s('devis.step8', 'Où vous recontacter ?'),
])
@section('content')
    @include('front.partials.hero', ['eyebrow' => s('devis.eyebrow', 'Devis gratuit · réponse rapide'), 'h1' => s('devis.h1', 'Votre devis poêle *en 2 minutes*'), 'intro' => s('devis.intro', 'Quelques questions pour préparer la visite technique. Le magasin le plus proche vous rappelle, sans engagement.')])

    <section class="content">
        <div class="wrap">
            <form class="wizard" method="post" action="/devis/" enctype="multipart/form-data" data-wizard data-postcodes='@json($postcodes)' novalidate>
                @csrf
                <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="products" value="{{ $product ? json_encode([['id' => $product->id, 'name' => $product->name, 'brand' => $product->brand?->name]]) : '' }}" data-products-input>

                @if($errors->any())
                    <div class="errors" role="alert"><strong>{{ s('form.errors', 'Merci de vérifier les champs suivants :') }}</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                <div class="progress js-only" data-progress aria-live="polite">
                    <div class="progress-txt"><span data-progress-label></span><span data-progress-count></span></div>
                    <div class="progress-bar"><i data-progress-bar></i></div>
                </div>

                <div class="selection" data-selection @unless($product) hidden @endunless>
                    <strong>{{ s('devis.selection', 'Votre sélection') }}</strong>
                    <span data-selection-list>@if($product){{ $product->name }}@if($product->brand) ({{ $product->brand->name }})@endif @endif</span>
                </div>

                @foreach(['project_type' => 1, 'appliance_type' => 2, 'energy' => 3, 'replacement' => 4] as $field => $n)
                    <fieldset class="step-panel @if($n === 1) on @endif" data-step="{{ $n }}" @if($field === 'replacement') data-if="project_type=remplacement" @endif>
                        <legend>{{ $steps[$n] }}</legend>
                        <div class="choices">
                            @foreach($options[$field] as $value => $label)
                                <label class="choice"><input type="radio" name="{{ $field }}" value="{{ $value }}" @checked(old($field, $field === 'appliance_type' ? $product?->appliance_type : ($field === 'energy' ? $product?->energy : null)) === $value)><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach

                <fieldset class="step-panel" data-step="5">
                    <legend>{{ $steps[5] }}</legend>
                    <div class="form-grid cols-2">
                        <div class="field"><label for="surface_m2">{{ s('devis.surface', 'Surface à chauffer (m²)') }}</label>
                            <input id="surface_m2" type="number" name="surface_m2" min="5" max="2000" inputmode="numeric" value="{{ old('surface_m2') }}"></div>
                        <div class="field"><label for="floor">{{ s('devis.floor', 'Emplacement de l\'appareil') }}</label>
                            <select id="floor" name="floor"><option value="">—</option>@foreach($options['floor'] as $value => $label)<option value="{{ $value }}" @selected(old('floor') === $value)>{{ $label }}</option>@endforeach</select></div>
                    </div>
                    <div class="field"><span class="field-label" style="font-weight:600;font-size:.92rem">{{ s('devis.flue', 'Avez-vous déjà un conduit de cheminée ?') }}</span>
                        <div class="choices">
                            @foreach(['oui' => s('devis.flue_yes', 'Oui'), 'non' => s('devis.flue_no', 'Non'), 'nsp' => s('devis.flue_nsp', 'Je ne sais pas')] as $value => $label)
                                <label class="choice"><input type="radio" name="has_flue" value="{{ $value }}" @checked(old('has_flue') === $value)><span>{{ $label }}</span></label>
                            @endforeach
                        </div></div>
                </fieldset>

                <fieldset class="step-panel" data-step="6">
                    <legend>{{ $steps[6] }}</legend>
                    <div class="choices">
                        <label class="choice"><input type="radio" name="brand_id" value="" @checked(!old('brand_id', $product?->brand_id))><span>{{ s('devis.no_brand', 'Pas de préférence') }}</span></label>
                        @foreach($brands as $brand)
                            <label class="choice"><input type="radio" name="brand_id" value="{{ $brand->id }}" @checked((int) old('brand_id', $product?->brand_id) === $brand->id)><span>{{ $brand->name }}</span></label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="step-panel" data-step="7">
                    <legend>{{ $steps[7] }}</legend>
                    <div class="form-grid cols-2">
                        <div class="field"><label for="budget">{{ s('devis.budget', 'Budget envisagé (pose comprise)') }}</label>
                            <select id="budget" name="budget"><option value="">—</option>@foreach($options['budget'] as $value => $label)<option value="{{ $value }}" @selected(old('budget') === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div class="field"><label for="delay">{{ s('devis.delay', 'Délai souhaité') }}</label>
                            <select id="delay" name="delay"><option value="">—</option>@foreach($options['delay'] as $value => $label)<option value="{{ $value }}" @selected(old('delay') === $value)>{{ $label }}</option>@endforeach</select></div>
                    </div>
                </fieldset>

                <fieldset class="step-panel" data-step="8">
                    <legend>{{ $steps[8] }}</legend>
                    <div class="form-grid cols-2">
                        <div class="field"><label for="name">{{ s('form.name', 'Nom et prénom') }} *</label><input id="name" type="text" name="name" required autocomplete="name" value="{{ old('name') }}"></div>
                        <div class="field"><label for="phone">{{ s('form.phone', 'Téléphone') }} *</label><input id="phone" type="tel" name="phone" required autocomplete="tel" value="{{ old('phone') }}"></div>
                        <div class="field"><label for="email">{{ s('form.email', 'E-mail') }} *</label><input id="email" type="email" name="email" required autocomplete="email" value="{{ old('email') }}"></div>
                        <div class="field"><label for="postal_code">{{ s('form.postal_code', 'Code postal') }} *</label><input id="postal_code" type="text" name="postal_code" required inputmode="numeric" pattern="\d{5}" maxlength="5" autocomplete="postal-code" value="{{ old('postal_code') }}" data-cp></div>
                        <div class="field"><label for="store_id">{{ s('form.store', 'Magasin préféré') }}</label>
                            <select id="store_id" name="store_id" data-store-select><option value="">{{ s('form.store_auto', 'Le plus proche de chez moi') }}</option>
                                @foreach($stores as $st)<option value="{{ $st->id }}" @selected((int) old('store_id', $preStore?->id) === $st->id)>{{ $st->area_label ?: $st->short_name }}</option>@endforeach</select>
                            <span class="hint">{{ s('form.store_hint', 'Proposé automatiquement selon votre code postal, modifiable.') }}</span></div>
                        <div class="field"><label for="photos">{{ s('form.photos', 'Photos du lieu (facultatif, 5 max.)') }}</label><input id="photos" type="file" name="photos[]" accept="image/*" multiple></div>
                    </div>
                    <div class="field"><label for="message">{{ s('form.message', 'Un détail à nous préciser ?') }}</label><textarea id="message" name="message" rows="4">{{ old('message') }}</textarea></div>
                    <label class="consent"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))><span>{{ s('form.consent', 'J\'accepte que Braises Émeraude utilise ces informations pour me recontacter au sujet de mon projet. Elles ne sont jamais transmises à des tiers.') }} <a href="/politique-de-confidentialite/" style="text-decoration:underline">{{ s('form.privacy', 'Politique de confidentialité') }}</a></span></label>
                </fieldset>

                <div class="wizard-nav">
                    <button class="btn btn-ghost js-only" type="button" data-prev>← {{ s('form.prev', 'Retour') }}</button>
                    <button class="btn btn-main js-only" type="button" data-next>{{ s('form.next', 'Continuer') }} →</button>
                    <button class="btn btn-main" type="submit" data-submit>{{ s('devis.submit', 'Envoyer ma demande de devis') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
