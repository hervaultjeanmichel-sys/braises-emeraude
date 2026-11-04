<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * JM, 04/11/2026 : pas de devis en magasin — le showroom sert à voir les modèles et les prix moyens,
 * le devis passe TOUJOURS par la visite d'un technicien à domicile. Réécriture des phrases ambiguës
 * des pages magasins (Dinard, Saint-Malo, Rennes) et ville (Cancale). Le reste du texte n'est pas touché.
 */
return new class extends Migration
{
    /** Motif tolérant : espaces, &nbsp;, balises et apostrophes typographiques entre les mots. */
    private function pattern(string $phrase, bool $keepLink = false): string
    {
        $sep = '(?:\s|&nbsp;|<[^>]+>)*';
        $words = preg_split('/\s+/u', $phrase);
        $parts = array_map(function (string $w) {
            $w = preg_quote($w, '/');

            return str_replace(["'", '’'], ["(?:'|’|&rsquo;|&#0?39;)", "(?:'|’|&rsquo;|&#0?39;)"], $w);
        }, $words);

        return '/'.implode($sep, $parts).'/u';
    }

    public function up(): void
    {
        $rules = [
            'Passez nous voir au showroom ou demandez votre devis gratuit en ligne : votre demande arrive directement à l’équipe de Dinard · Pleurtuit.'
                => 'Passez nous voir au showroom pour découvrir les modèles en fonctionnement. Pour votre devis, demandez la visite technique gratuite : un technicien de l’équipe de Dinard · Pleurtuit vient chez vous contrôler la faisabilité et chiffrer l’installation.',
            'Passez nous voir au showroom ou demandez un devis gratuit.'
                => 'Passez nous voir au showroom pour découvrir les modèles. Pour votre devis, un technicien vient gratuitement chez vous contrôler la faisabilité et chiffrer l’installation.',
            'Un devis gratuit vous permet d’avoir un chiffrage précis.'
                => 'Pour un chiffrage précis, un technicien se déplace gratuitement chez vous : nous ne réalisons pas de devis en magasin.',
            'pour un devis gratuit ou pour prendre rendez-vous dans l’un de nos showrooms.'
                => 'pour programmer la visite technique gratuite à votre domicile : c’est elle qui permet d’établir votre devis. Nos showrooms vous accueillent pour découvrir les modèles.',
        ];

        foreach (['stores', 'cities'] as $table) {
            foreach (DB::table($table)->whereNotNull('content')->get(['id', 'content']) as $row) {
                $html = (string) $row->content;
                $new = $html;
                foreach ($rules as $from => $to) {
                    $new = preg_replace($this->pattern($from), $to, $new) ?? $new;
                }
                if ($new !== $html) {
                    DB::table($table)->where('id', $row->id)->update(['content' => $new, 'updated_at' => now()]);
                }
            }
        }
    }

    public function down(): void
    {
    }
};
