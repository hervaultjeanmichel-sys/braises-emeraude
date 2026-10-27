<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\FormOption;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Store;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * Formulaires publics : devis en 8 étapes, contact, liste « Mon projet ».
 * Toute demande devient une fiche Lead en base, routée vers un magasin.
 */
class LeadController extends Controller
{
    private function seo(string $titleKey, string $titleDefault, string $url, ?string $descKey = null): Seo
    {
        $seo = new Seo;
        $seo->title = s($titleKey, $titleDefault);
        $seo->description = $descKey ? s($descKey) : null;
        $seo->canonical = $url;
        $seo->robots = 'noindex,follow'; // pages utilitaires
        $seo->breadcrumb = [['label' => $seo->title, 'url' => $url]];

        return $seo;
    }

    public function quote(Request $request): View
    {
        $product = $request->integer('produit') ? Product::published()->with('brand')->find($request->integer('produit')) : null;
        $store = $request->integer('magasin') ? Store::published()->find($request->integer('magasin')) : null;

        $seo = $this->seo('devis.seo_title', 'Demande de devis gratuit', '/devis/', 'devis.seo_description');
        $seo->robots = 'index,follow'; // la page devis peut se positionner (« devis poêle à granulés »)

        return view('front.quote', [
            'seo' => $seo,
            'options' => collect(['project_type', 'appliance_type', 'energy', 'replacement', 'floor', 'budget', 'delay'])
                ->mapWithKeys(fn ($l) => [$l => FormOption::listFor($l)]),
            'brands' => Brand::published()->orderBy('name')->get(),
            'stores' => Store::published()->orderBy('sort')->get(),
            'product' => $product,
            'preStore' => $store,
        ]);
    }

    public function storeQuote(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) { // pot de miel anti-robots
            return new RedirectResponse('/devis/merci/');
        }

        $opt = fn (string $list) => ['nullable', Rule::in(array_keys(FormOption::listFor($list)))];
        $data = $request->validate([
            'project_type' => $opt('project_type'),
            'appliance_type' => $opt('appliance_type'),
            'energy' => $opt('energy'),
            'replacement' => $opt('replacement'),
            'surface_m2' => ['nullable', 'integer', 'min:5', 'max:2000'],
            'has_flue' => ['nullable', Rule::in(['oui', 'non', 'nsp'])],
            'floor' => $opt('floor'),
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'budget' => $opt('budget'),
            'delay' => $opt('delay'),
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'postal_code' => ['required', 'regex:/^\d{5}$/'],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'message' => ['nullable', 'string', 'max:3000'],
            'products' => ['nullable', 'string', 'max:10000'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:8192'],
            'consent' => ['accepted'],
        ], [], $this->attributeNames());

        return $this->createLead('devis', $data, $request);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return new RedirectResponse('/devis/merci/');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'postal_code' => ['nullable', 'regex:/^\d{5}$/'],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'message' => ['required', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ], [], $this->attributeNames());

        return $this->createLead('contact', $data, $request);
    }

    public function thanks(): View
    {
        return view('front.thanks', ['seo' => $this->seo('devis.thanks_title', 'Merci, votre demande est bien arrivée', '/devis/merci/')]);
    }

    public function project(): View
    {
        return view('front.project', ['seo' => $this->seo('project.title', 'Mon projet', '/mon-projet/')]);
    }

    private function createLead(string $type, array $data, Request $request): RedirectResponse
    {
        // Liste « Mon projet » : seuls les produits réellement en base sont conservés
        $products = [];
        foreach (json_decode((string) ($data['products'] ?? ''), true) ?: [] as $item) {
            $p = isset($item['id']) ? Product::with('brand')->find((int) $item['id']) : null;
            if ($p) {
                $products[] = ['product_id' => $p->id, 'name' => $p->name, 'brand' => $p->brand?->name, 'note' => mb_substr((string) ($item['note'] ?? ''), 0, 300)];
            }
        }

        $photos = [];
        foreach ($request->file('photos', []) as $file) {
            $photos[] = $file->store('demandes/'.now()->format('Y/m'), 'local'); // privé, hors /public
        }

        $utm = array_filter($request->only(['utm_source', 'utm_medium', 'utm_campaign']) + (array) session('utm', []));
        $lead = Lead::create([
            ...collect($data)->except(['products', 'photos', 'consent'])->all(),
            'type' => $type,
            'status' => 'nouvelle',
            'products' => $products ?: null,
            'photos' => $photos ?: null,
            'consent_at' => now(),
            'source' => $this->source($utm, (string) session('referer', '')),
            'utm' => $utm ?: null,
            'ip_hash' => hash('sha256', (string) $request->ip().config('app.key')),
        ]);

        $this->notify($lead);

        session()->flash('lead_type', $type);

        return new RedirectResponse('/devis/merci/');
    }

    private function source(array $utm, string $referer): string
    {
        if (! empty($utm['utm_campaign']) && str_contains(strtolower($utm['utm_campaign']), 'rennes')) {
            return 'campagne-rennes';
        }
        if (! empty($utm['utm_source'])) {
            return mb_substr(strtolower($utm['utm_source']), 0, 40);
        }

        return match (true) {
            str_contains($referer, 'google.') => 'google',
            str_contains($referer, 'facebook.') || str_contains($referer, 'instagram.') => 'reseaux',
            $referer !== '' => 'site-referent',
            default => 'direct',
        };
    }

    /** E-mail au magasin destinataire (repli : adresse générale). Un échec d'envoi ne bloque jamais la demande. */
    private function notify(Lead $lead): void
    {
        $to = $lead->store?->email ?: s('leads.fallback_email');
        if (! $to) {
            return;
        }
        $lines = [
            'Nouvelle demande ('.(Lead::TYPES[$lead->type] ?? $lead->type).') — '.($lead->store?->short_name ?? 'magasin à attribuer'),
            '',
            'Nom : '.$lead->name,
            'Téléphone : '.$lead->phone,
            'E-mail : '.$lead->email,
            'Code postal : '.$lead->postal_code,
            $lead->project_type ? 'Projet : '.(FormOption::listFor('project_type')[$lead->project_type] ?? $lead->project_type) : null,
            $lead->energy ? 'Énergie : '.(FormOption::listFor('energy')[$lead->energy] ?? $lead->energy) : null,
            $lead->surface_m2 ? 'Surface : '.$lead->surface_m2.' m²' : null,
            $lead->products ? 'Sélection : '.collect($lead->products)->pluck('name')->implode(', ') : null,
            $lead->message ? "\nMessage :\n".$lead->message : null,
            '',
            'Fiche : '.url('/admin/leads/'.$lead->id.'/edit'),
        ];
        try {
            Mail::raw(implode("\n", array_filter($lines, fn ($l) => $l !== null)), function ($m) use ($to, $lead) {
                $m->to($to)->subject('[Site] Demande de '.$lead->name.' — '.($lead->postal_code ?? ''));
                if ($lead->email) {
                    $m->replyTo($lead->email, $lead->name);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('Notification demande non envoyée', ['lead' => $lead->id, 'error' => $e->getMessage()]);
        }
    }

    private function attributeNames(): array
    {
        return [
            'name' => 'nom', 'phone' => 'téléphone', 'email' => 'e-mail', 'postal_code' => 'code postal',
            'surface_m2' => 'surface', 'message' => 'message', 'consent' => 'consentement', 'photos.*' => 'photo',
        ];
    }
}
