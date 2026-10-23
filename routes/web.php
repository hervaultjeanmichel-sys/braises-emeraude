<?php

declare(strict_types=1);

use App\Http\Controllers\FrontController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| Routage public — URLs WordPress conservées à l'identique (slash final géré par LegacyUrls).
| Ordre : routes spécifiques d'abord, résolution générique /{slug}/ en dernier.
*/

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/', [FrontController::class, 'home'])->name('home');

Route::get('/produit/{slug}', [FrontController::class, 'product'])->name('product');
Route::get('/categorie-produit/{path}', [FrontController::class, 'category'])->where('path', '[a-z0-9\-/]+')->name('category');
Route::get('/marque/{slug}', [FrontController::class, 'brand'])->name('brand');
Route::get('/aides/{slug}', [FrontController::class, 'aid'])->name('aid');

Route::get('/{year}/{month}/{day}/{slug}', [FrontController::class, 'post'])
    ->where(['year' => '\d{4}', 'month' => '\d{2}', 'day' => '\d{2}'])
    ->name('post');

// Pages CMS, pages magasins et pages villes partagent le même format /{slug}/
Route::get('/{slug}', [FrontController::class, 'resolve'])->where('slug', '[a-z0-9\-]+')->name('resolve');
