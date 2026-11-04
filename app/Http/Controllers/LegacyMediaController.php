<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\LegacyMedia;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Anciennes images / PDF WordPress (/wp-content/uploads/…) : 301 vers la copie rapatriée
 * par braises:clean-html (storage/wp/…), pour conserver Google Images et les liens externes.
 */
class LegacyMediaController extends Controller
{
    public function __invoke(string $path): RedirectResponse
    {
        $target = LegacyMedia::localUrl('/wp-content/uploads/'.$path);
        if (! $target) {
            throw new NotFoundHttpException();
        }

        return new RedirectResponse($target, 301);
    }
}
