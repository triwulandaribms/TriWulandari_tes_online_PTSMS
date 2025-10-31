<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Guards\JWTGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Daftarkan driver 'jwt' untuk guard custom (misalnya 'admin')
         * agar Laravel tahu cara memproses autentikasi JWT
         */
        Auth::extend('jwt', function ($app, $name, array $config) {
            // Buat provider user (misalnya Admin atau User)
            $provider = Auth::createUserProvider($config['provider'] ?? null);

            // Kembalikan instance JWTGuard baru
            return new JWTGuard(
                $provider,
                $app['tymon.jwt'],     // instance JWTAuth utama
                $app['request']
            );
        });
    }
}
