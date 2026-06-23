<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\SupportTicket;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\WishlistService;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class        => OrderPolicy::class,
        SupportTicket::class => TicketPolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(WishlistService::class);
    }

    public function boot(): void
    {
        // Super Admin bypasses all permission checks
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }
        });

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Share settings and cart/wishlist counts with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $cartService     = app(CartService::class);
                $wishlistService = app(WishlistService::class);
                $view->with('cartCount',     $cartService->getCount(auth()->user()));
                $view->with('wishlistCount', $wishlistService->getCount(auth()->user()));
            } else {
                $view->with('cartCount', 0);
                $view->with('wishlistCount', 0);
            }

            try {
                $view->with('siteName',       Setting::get('site_name', 'ShopGram'));
                $view->with('siteLogo',       Setting::get('site_logo'));
                $view->with('currencySymbol', Setting::get('currency_symbol', '৳'));
            } catch (\Exception $e) {
                $view->with('siteName', 'ShopGram');
                $view->with('siteLogo', null);
                $view->with('currencySymbol', '৳');
            }
        });
    }
}
