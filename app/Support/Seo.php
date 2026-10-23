<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use App\Models\Store;

/**
 * Construit le <head> SEO et le JSON-LD de chaque page.
 * Le contrôleur remplit, le layout affiche (title, description, canonical, robots, schémas).
 */
final class Seo
{
    public string $title = '';

    public ?string $description = null;

    public string $canonical = '/';

    public string $robots = 'index,follow';

    public ?string $image = null;

    /** @var array<int, array{label:string, url:string}> */
    public array $breadcrumb = [];

    /** @var array<int, array<string,mixed>> */
    public array $schemas = [];

    public static function for(object $model, array $breadcrumb = []): self
    {
        $seo = new self;
        $seo->title = method_exists($model, 'metaTitle') ? $model->metaTitle() : '';
        $seo->description = method_exists($model, 'metaDescription') ? $model->metaDescription() : null;
        $seo->canonical = method_exists($model, 'url') ? $model->url() : '/';
        $seo->robots = $model->robots ?? 'index,follow';
        $seo->breadcrumb = $breadcrumb;

        return $seo;
    }

    public function fullTitle(): string
    {
        $suffix = Setting::get('seo.title_suffix', ' · Braises Émeraude');

        return $this->title && ! str_contains($this->title, 'Braises Émeraude') ? $this->title.$suffix : ($this->title ?: 'Braises Émeraude');
    }

    public function absolute(string $path): string
    {
        return rtrim((string) config('app.url'), '/').$path;
    }

    /** Organization (le Groupe) + un LocalBusiness par magasin, reliés entre eux. */
    public function groupSchema(): array
    {
        $base = $this->absolute('/');
        $stores = Store::published()->orderBy('sort')->get();

        $graph = [[
            '@type' => 'Organization',
            '@id' => $base.'#groupe',
            'name' => Setting::get('group.name', 'Braises Émeraude'),
            'legalName' => Setting::get('group.legal_name', 'La Soleillade'),
            'url' => $base,
            'logo' => $this->absolute(media(Setting::get('group.logo') ?: '/img/logo.webp') ?? '/img/logo.webp'),
            'slogan' => Setting::get('group.slogan'),
            'subOrganization' => $stores->map(fn ($s) => ['@id' => $base.'#magasin-'.$s->id])->all(),
        ]];

        foreach ($stores as $s) {
            $graph[] = array_filter([
                '@type' => 'HVACBusiness',
                '@id' => $base.'#magasin-'.$s->id,
                'name' => $s->name,
                'parentOrganization' => ['@id' => $base.'#groupe'],
                'url' => $this->absolute($s->url()),
                'telephone' => $s->phone,
                'email' => $s->email,
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'streetAddress' => $s->address,
                    'postalCode' => $s->postal_code,
                    'addressLocality' => $s->city,
                    'addressRegion' => 'Bretagne',
                    'addressCountry' => 'FR',
                ]),
                'geo' => $s->latitude ? ['@type' => 'GeoCoordinates', 'latitude' => $s->latitude, 'longitude' => $s->longitude] : null,
                'openingHoursSpecification' => collect($s->opening_hours ?? [])->map(fn ($h) => [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][(int) $h['day']] ?? null,
                    'opens' => $h['opens'] ?? null,
                    'closes' => $h['closes'] ?? null,
                ])->values()->all() ?: null,
                'areaServed' => $s->served_communes ?: null,
            ]);
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    public function breadcrumbSchema(): ?array
    {
        if ($this->breadcrumb === []) {
            return null;
        }
        $items = [['label' => 'Accueil', 'url' => '/'], ...$this->breadcrumb];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($b, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $b['label'],
                'item' => $this->absolute($b['url']),
            ])->all(),
        ];
    }

    public static function faqSchema(?array $faq): ?array
    {
        if (empty($faq)) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faq)->map(fn ($q) => [
                '@type' => 'Question',
                'name' => $q['question'] ?? '',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q['answer'] ?? ''],
            ])->values()->all(),
        ];
    }

    /** @return array<int, array<string,mixed>> */
    public function allSchemas(): array
    {
        return array_values(array_filter([$this->groupSchema(), $this->breadcrumbSchema(), ...$this->schemas]));
    }
}
