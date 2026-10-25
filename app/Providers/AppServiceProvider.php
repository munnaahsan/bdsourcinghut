<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('frontend.*', static function (BladeView $view): void {
            // Preserve data already supplied by controllers.
            $data = $view->getData();

            // Provide safe defaults for shared layout variables.
            $view->with([
                'site' => $data['site'] ?? [],
                'theme' => $data['theme'] ?? [],

                'logoMedia' => $data['logoMedia'] ?? null,
                'faviconMedia' => $data['faviconMedia'] ?? null,

                'mainMenu' => $data['mainMenu'] ?? null,
                'footerMenu' => $data['footerMenu'] ?? null,

                // Shared image URL helper.
                'imageUrl' => static function (?string $path): string {
                    if (empty($path)) {
                        return '';
                    }

                    // Preserve external image URLs.
                    if (preg_match('~^https?://~i', $path)) {
                        return $path;
                    }

                    $path = ltrim($path, '/');

                    // Images stored directly in public/.
                    if (
                        str_starts_with($path, 'images/') ||
                        str_starts_with($path, 'storage/')
                    ) {
                        return asset($path);
                    }

                    // Uploaded images stored on Laravel's public disk.
                    return Storage::disk('public')->url($path);
                },
            ]);
        });
    }
}
