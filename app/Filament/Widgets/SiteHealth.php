<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\AidTopic;
use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Product;
use App\Models\Store;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** État du site : configuration, contenus, magasins, demandes. ok = vert, warn = orange, error = rouge, info = gris. */
class SiteHealth extends Widget
{
    /** Réservé à l'administrateur du groupe (pas aux équipes magasin). */
    public static function canView(): bool
    {
        return (bool) auth()->user()?->isGroupAdmin();
    }

    protected static ?int $sort = 6;

    protected static string $view = 'filament.widgets.site-health';

    protected static ?string $pollingInterval = '300s';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $c = [];
        $prod = app()->environment('production');

        // Configuration
        $c[] = ['Environnement', $prod ? 'ok' : 'info', $prod ? 'Production' : 'Préprod / '.app()->environment().' — non indexée par Google (normal)', null];
        $c[] = ['Mode débogage', config('app.debug') ? ($prod ? 'error' : 'warn') : 'ok', config('app.debug') ? 'Activé : à couper en production (APP_DEBUG=false)' : 'Désactivé', null];
        $c[] = ['Versions', 'info', 'PHP '.PHP_VERSION.' · Laravel '.app()->version(), null];
        $free = @disk_free_space(base_path());
        $c[] = ['Espace disque libre', $free !== false && $free < 2 * 1024 ** 3 ? 'warn' : 'ok', $free !== false ? round($free / 1024 ** 3, 1).' Go' : 'inconnu', null];
        $link = is_link(public_path('storage')) || is_dir(public_path('storage'));
        $c[] = ['Images envoyées (lien storage)', $link ? 'ok' : 'error', $link ? 'En place' : 'Manquant : php artisan storage:link', null];

        // Magasins
        $stores = Store::where('is_published', true)->get();
        $noMail = $stores->filter(fn ($s) => blank($s->email))->pluck('short_name');
        $c[] = ['E-mail des magasins', $noMail->isEmpty() ? 'ok' : 'warn', $noMail->isEmpty() ? 'Tous renseignés' : 'Manquant : '.$noMail->implode(', ').' (les demandes partent vers l’adresse générale)', '/admin/stores'];
        $noGps = $stores->filter(fn ($s) => ! $s->latitude || ! $s->longitude)->pluck('short_name');
        $c[] = ['Coordonnées GPS (cartes)', $noGps->isEmpty() ? 'ok' : 'warn', $noGps->isEmpty() ? 'Tous renseignés' : 'Manquant : '.$noGps->implode(', '), '/admin/stores'];

        // Demandes
        if (Schema::hasTable('appointments')) {
            $late = Appointment::where('status', 'en_attente')->where('created_at', '<', now()->subHours(48))->count();
            $c[] = ['Rendez-vous en attente depuis plus de 48 h', $late ? 'warn' : 'ok', $late ? $late.' demande(s) à confirmer ou refuser' : 'Aucun', '/admin/appointments'];
        }
        $lastLead = Lead::latest()->value('created_at');
        $c[] = ['Dernière demande reçue (devis / contact)', 'info', $lastLead ? \Carbon\Carbon::parse($lastLead)->locale('fr')->diffForHumans() : 'aucune', '/admin/leads'];

        // Contenus et SEO
        $published = Product::where('is_published', true)->count();
        $thin = Product::where('is_published', true)->get(['content', 'excerpt'])->filter(fn ($p) => mb_strlen(strip_tags((string) $p->content.' '.$p->excerpt)) < 1500)->count();
        $c[] = ['Fiches produits peu décrites (< 250 mots)', $thin > $published / 2 ? 'warn' : ($thin ? 'info' : 'ok'), $thin.' / '.$published, '/admin/products'];
        $noDesc = Page::where('is_published', true)->where(fn ($q) => $q->whereNull('seo_description')->orWhere('seo_description', ''))->pluck('slug');
        $c[] = ['Pages sans meta description', $noDesc->isEmpty() ? 'ok' : 'warn', $noDesc->isEmpty() ? 'Aucune' : $noDesc->count().' : '.$noDesc->take(6)->map(fn ($s) => $s === '/' ? 'accueil' : $s)->implode(', ').($noDesc->count() > 6 ? '…' : ''), '/admin/pages'];
        $old = 0;
        foreach (['pages', 'posts', 'products', 'cities', 'stores', 'product_categories', 'brands'] as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'content')) {
                $old += DB::table($t)->where('content', 'like', '%braisesemeraude.com/wp-content%')->count();
            }
        }
        $c[] = ['Images encore hébergées sur l’ancien site', $old ? 'error' : 'ok', $old ? $old.' fiche(s) : elles casseront à la mise en ligne → php artisan braises:clean-html --apply' : 'Aucune', null];
        if (Schema::hasTable('aid_topics')) {
            $stale = AidTopic::where('is_published', true)->where(fn ($q) => $q->whereNull('verified_on')->orWhere('verified_on', '<', now()->subMonths(12)))->count();
            $c[] = ['Fiches aides à revérifier (> 12 mois)', $stale ? 'warn' : 'ok', $stale ? $stale.' fiche(s)' : 'À jour', '/admin/aid-topics'];
        }
        if (Schema::hasTable('visits')) {
            $last = DB::table('visits')->max('last_seen_at');
            $c[] = ['Mesure d’audience', $last ? 'ok' : 'info', $last ? 'Dernière visite '.\Carbon\Carbon::parse($last)->locale('fr')->diffForHumans() : 'Aucune visite enregistrée pour l’instant', null];
        }

        return ['checks' => $c, 'score' => collect($c)->whereIn(1, ['ok', 'info'])->count(), 'total' => count($c)];
    }
}
