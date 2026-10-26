<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Rendez-vous en ligne : toujours « en attente » jusqu'à validation par l'équipe du magasin. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->string('type_key', 60);                      // clé du type dans booking_settings.types
            $t->string('type_label');                        // libellé figé au moment de la demande
            $t->boolean('at_home')->default(false);          // showroom | domicile
            $t->dateTime('starts_at');                       // heure de Paris
            $t->dateTime('ends_at');
            $t->string('status', 20)->default('en_attente'); // en_attente | confirme | refuse | annule
            $t->string('name');
            $t->string('phone', 30);
            $t->string('email');
            $t->string('address')->nullable();
            $t->string('postal_code', 10)->nullable();
            $t->string('city')->nullable();
            $t->text('message')->nullable();
            $t->text('team_note')->nullable();               // note interne / motif de refus
            $t->string('source', 40)->nullable();
            $t->timestamp('consent_at')->nullable();
            $t->timestamp('confirmed_at')->nullable();
            $t->string('google_event_id')->nullable();       // branchement Google Agenda (étape suivante)
            $t->string('ip_hash', 64)->nullable();
            $t->timestamps();
            $t->index(['store_id', 'starts_at']);
            $t->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
