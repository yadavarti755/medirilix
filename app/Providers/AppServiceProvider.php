<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('cart', function () {
            return new \App\Services\CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Override the mail manager to disable TLS flag but keep STARTTLS
        $this->app->extend('mail.manager', function ($manager, $app) {
            return new class($app) extends MailManager {
                protected function createSmtpTransport(array $config): TransportInterface
                {
                    // Create transport with TLS disabled (no ssl:// prefix)
                    // but STARTTLS will still work
                    $transport = new EsmtpTransport(
                        $config['host'],
                        $config['port'],
                        false // This prevents ssl:// prefix
                    );

                    // Manually configure the stream to disable TLS prefix
                    $stream = $transport->getStream();
                    if (method_exists($stream, 'disableTls')) {
                        $stream->disableTls(); // This prevents ssl:// prefix
                    }

                    if (isset($config['username'])) {
                        $transport->setUsername($config['username']);
                        $transport->setPassword($config['password']);
                    }

                    if (isset($config['timeout'])) {
                        $transport->setTimeout($config['timeout']);
                    }

                    return $transport;
                }
            };
        });

        // Implicitly grant "SUPERADMIN" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('SUPERADMIN') ? true : null;
        });
        View::share('siteSettings', SiteSetting::with('currency')->first());
    }
}
