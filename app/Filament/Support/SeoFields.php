<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

/**
 * Blocs de formulaire partagés par toutes les ressources publiques.
 * Règle : slug + seo_title + seo_description + robots sur chaque entité.
 */
final class SeoFields
{
    public static function slug(string $prefix = '/', string $suffix = '/'): TextInput
    {
        return TextInput::make('slug')
            ->label('Slug (URL)')
            ->required()
            ->maxLength(190)
            ->unique(ignoreRecord: true)
            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
            ->prefix($prefix)
            ->suffix($suffix)
            ->helperText('Minuscules, chiffres et tirets. Changer un slug existant = créer une redirection 301.');
    }

    public static function section(): Section
    {
        return Section::make('Référencement')
            ->icon('heroicon-o-magnifying-glass')
            ->collapsible()
            ->schema([
                TextInput::make('seo_title')
                    ->label('Titre SEO')
                    ->maxLength(70)
                    ->live(debounce: 500)
                    ->helperText(fn (Get $get) => mb_strlen((string) $get('seo_title')).' / 60 caractères conseillés'),
                Textarea::make('seo_description')
                    ->label('Description SEO')
                    ->rows(3)
                    ->maxLength(320)
                    ->live(debounce: 500)
                    ->helperText(fn (Get $get) => mb_strlen((string) $get('seo_description')).' / 155 caractères conseillés'),
                Select::make('robots')
                    ->label('Robots')
                    ->options([
                        'index,follow' => 'index, follow (par défaut)',
                        'noindex,follow' => 'noindex, follow (exclu du sitemap)',
                        'noindex,nofollow' => 'noindex, nofollow',
                    ])
                    ->default('index,follow')
                    ->required(),
                Toggle::make('is_published')->label('Publié')->default(true),
            ]);
    }

    /** Titres et texte riche éditables (H1 + intro + contenu avec H2/H3). */
    public static function content(bool $eyebrow = true): array
    {
        return array_values(array_filter([
            $eyebrow ? TextInput::make('eyebrow')->label('Sur-titre (eyebrow)')->maxLength(120) : null,
            TextInput::make('h1')->label('Titre H1')->maxLength(190)->helperText('Un seul H1 par page.'),
            Textarea::make('intro')->label('Chapô / introduction')->rows(3),
            RichEditor::make('content')
                ->label('Contenu (H2, H3, listes, liens)')
                ->toolbarButtons(['h2', 'h3', 'bold', 'italic', 'bulletList', 'orderedList', 'link', 'blockquote', 'undo', 'redo'])
                ->columnSpanFull(),
        ]));
    }

    public static function faq(): Repeater
    {
        return Repeater::make('faq')
            ->label('FAQ (balisage FAQPage)')
            ->schema([
                TextInput::make('question')->required(),
                Textarea::make('answer')->label('Réponse')->rows(3)->required(),
            ])
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
            ->defaultItems(0)
            ->columnSpanFull();
    }

    public static function wordCount(int $min = 600): Placeholder
    {
        return Placeholder::make('word_count')
            ->label('Volume de texte')
            ->content(function (Get $get) use ($min) {
                $n = str_word_count(strip_tags((string) $get('intro').' '.(string) $get('content')));
                $color = $n >= $min ? '#34e3a5' : '#ff7b30';

                return new HtmlString("<span style=\"color:{$color};font-weight:600\">{$n} mots</span> — objectif {$min} minimum");
            });
    }
}
