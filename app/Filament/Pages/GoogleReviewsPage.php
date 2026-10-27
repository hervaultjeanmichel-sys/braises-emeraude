<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Store;
use App\Support\GoogleReviews;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/** Choix des avis Google affichés sur le site (affiché / masqué / mis en avant). */
class GoogleReviewsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Magasins';

    protected static ?string $navigationLabel = 'Avis Google';

    protected static ?string $title = 'Avis Google';

    protected static ?string $slug = 'avis-google';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.google-reviews';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isGroupAdmin();
    }

    public function toggleVisible(int $storeId, string $key, bool $visible): void
    {
        GoogleReviews::choose($storeId, $key, ['is_visible' => ! $visible]);
        Notification::make()->title($visible ? 'Avis masqué sur le site' : 'Avis affiché sur le site')->success()->send();
    }

    public function toggleFeatured(int $storeId, string $key, bool $featured): void
    {
        GoogleReviews::choose($storeId, $key, ['is_featured' => ! $featured]);
        Notification::make()->title($featured ? 'Avis retiré de la mise en avant' : 'Avis mis en avant')->success()->send();
    }

    public function refreshFromGoogle(): void
    {
        Store::all()->each(fn ($s) => GoogleReviews::forget($s));
        Notification::make()->title('Avis rechargés depuis Google')->success()->send();
    }

    protected function getViewData(): array
    {
        $stores = Store::where('is_published', true)->orderBy('sort')->get();

        return [
            'enabled' => GoogleReviews::enabled(),
            'summary' => GoogleReviews::summary($stores, true),
            'stores' => $stores,
            'data' => $stores->mapWithKeys(fn ($s) => [$s->id => GoogleReviews::forStore($s, true)]),
        ];
    }
}
