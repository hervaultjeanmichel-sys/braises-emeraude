<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Effets animés saisonniers (neige, feux d'artifice, Halloween…) avec dates de début / fin. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasonal_effects', function (Blueprint $t) {
            $t->id();
            $t->string('name');                               // « Noël »
            $t->string('effect', 20);                         // snow | fireworks | halloween | easter | hearts | leaves | confetti | embers
            $t->date('starts_on');
            $t->date('ends_on');                              // inclus
            $t->boolean('yearly')->default(true);             // revient chaque année aux mêmes dates
            $t->string('intensity', 10)->default('medium');   // low | medium | high
            $t->string('scope', 10)->default('all');          // all | home
            $t->boolean('is_active')->default(true);
            $t->unsignedSmallInteger('sort')->default(0);
            $t->timestamps();
        });

        // Calendrier de départ (modifiable dans l'admin). Pâques 2027 : dimanche 28 mars.
        $now = now();
        DB::table('seasonal_effects')->insert([
            ['name' => 'Halloween', 'effect' => 'halloween', 'starts_on' => '2026-10-25', 'ends_on' => '2026-10-31', 'yearly' => true, 'intensity' => 'medium', 'scope' => 'all', 'is_active' => true, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Noël', 'effect' => 'snow', 'starts_on' => '2026-12-01', 'ends_on' => '2026-12-26', 'yearly' => true, 'intensity' => 'medium', 'scope' => 'all', 'is_active' => true, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Nouvel an', 'effect' => 'fireworks', 'starts_on' => '2026-12-31', 'ends_on' => '2027-01-03', 'yearly' => true, 'intensity' => 'medium', 'scope' => 'all', 'is_active' => true, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Saint-Valentin', 'effect' => 'hearts', 'starts_on' => '2027-02-12', 'ends_on' => '2027-02-14', 'yearly' => true, 'intensity' => 'low', 'scope' => 'all', 'is_active' => true, 'sort' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pâques 2027', 'effect' => 'easter', 'starts_on' => '2027-03-26', 'ends_on' => '2027-03-29', 'yearly' => false, 'intensity' => 'medium', 'scope' => 'all', 'is_active' => true, 'sort' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Inauguration Rennes', 'effect' => 'embers', 'starts_on' => '2026-11-14', 'ends_on' => '2026-11-22', 'yearly' => false, 'intensity' => 'low', 'scope' => 'home', 'is_active' => false, 'sort' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seasonal_effects');
    }
};
