<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Vidéo du showroom : fichier (mp4/webm) ou lien YouTube / Vimeo, avec image d'attente. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $t) {
            $t->string('video')->nullable()->after('photos');
            $t->string('video_url')->nullable()->after('video');
            $t->string('video_poster')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $t) {
            $t->dropColumn(['video', 'video_url', 'video_poster']);
        });
    }
};
