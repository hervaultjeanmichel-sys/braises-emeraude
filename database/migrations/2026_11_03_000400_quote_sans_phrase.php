<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/* Signature « au poil » (JM, 03/11/2026) : la phrase secondaire « (Ou à poil, on ne juge pas.) » est retirée. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('pages')->whereNotNull('sections')->get(['id', 'sections']) as $page) {
            $sections = json_decode((string) $page->sections, true);
            if (! is_array($sections)) {
                continue;
            }
            $changed = false;
            foreach ($sections as $i => $block) {
                if (($block['type'] ?? null) === 'quote' && ! empty($block['data']['small']) && str_contains(mb_strtolower($block['data']['small']), 'on ne juge pas')) {
                    $sections[$i]['data']['small'] = null;
                    $changed = true;
                }
            }
            if ($changed) {
                DB::table('pages')->where('id', $page->id)->update(['sections' => json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
    }
};
