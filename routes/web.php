<?php

declare(strict_types=1);

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| Routage public — URLs WordPress conservées à l'identique (slash final géré par LegacyUrls).
| Ordre : routes spécifiques d'abord, résolution générique /{slug}/ en dernier.
*/

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/', [FrontController::class, 'home'])->name('home');

// Demandes (formulaires) — avant la résolution générique /{slug}/
Route::get('/devis', [LeadController::class, 'quote'])->name('quote');
Route::post('/devis', [LeadController::class, 'storeQuote'])->middleware('throttle:6,1')->name('quote.store');
Route::get('/devis/merci', [LeadController::class, 'thanks'])->name('quote.thanks');
Route::post('/contact', [LeadController::class, 'storeContact'])->middleware('throttle:6,1')->name('contact.store');
Route::get('/mon-projet', [LeadController::class, 'project'])->name('project');

// Prise de rendez-vous (validation par l'équipe du magasin)
Route::get('/rendez-vous', [AppointmentController::class, 'show'])->name('booking');
Route::get('/rendez-vous/creneaux', [AppointmentController::class, 'slots'])->middleware('throttle:60,1')->name('booking.slots');
Route::post('/rendez-vous', [AppointmentController::class, 'store'])->middleware('throttle:6,1')->name('booking.store');
Route::get('/rendez-vous/merci', [AppointmentController::class, 'thanks'])->name('booking.thanks');

Route::get('/produit/{slug}', [FrontController::class, 'product'])->name('product');
Route::get('/categorie-produit/{path}', [FrontController::class, 'category'])->where('path', '[a-z0-9\-/]+')->name('category');
Route::get('/marque/{slug}', [FrontController::class, 'brand'])->name('brand');
Route::get('/aides/{slug}', [FrontController::class, 'aid'])->name('aid');

Route::get('/{year}/{month}/{day}/{slug}', [FrontController::class, 'post'])
    ->where(['year' => '\d{4}', 'month' => '\d{2}', 'day' => '\d{2}'])
    ->name('post');

// Pages CMS, pages magasins et pages villes partagent le même format /{slug}/
Route::get('/{slug}', [FrontController::class, 'resolve'])->where('slug', '[a-z0-9\-]+')->name('resolve');
