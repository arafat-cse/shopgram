<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class => OrderPolicy::class,
        SupportTicket::class => TicketPolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(WishlistService::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }
        });

        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        View::composer('*', function ($view) {
            if (auth()->check()) {
                $cartService = app(CartService::class);
                $wishlistService = app(WishlistService::class);

                $view->with('cartCount', $cartService->getCount(auth()->user()));
                $view->with('wishlistCount', $wishlistService->getCount(auth()->user()));
            } else {
                $view->with('cartCount', 0);
                $view->with('wishlistCount', 0);
            }

            try {
                $view->with('siteName', Setting::get('site_name', 'ShopGram'));
                $view->with('siteLogo', Setting::get('site_logo'));
                $view->with('currencySymbol', Setting::get('currency_symbol', '৳'));
            } catch (\Exception $e) {
                $view->with('siteName', 'ShopGram');
                $view->with('siteLogo', null);
                $view->with('currencySymbol', '৳');
            }

            try {
                $view->with('navCategories', Category::active()
                    ->parent()
                    ->with(['children' => fn($query) => $query->active()->orderBy('name')])
                    ->orderBy('name')
                    ->get());
            } catch (\Exception $e) {
                $view->with('navCategories', collect());
            }

            try {
                $view->with('footerPages', Page::active()->footer()->orderBy('title')->get());
            } catch (\Exception $e) {
                $view->with('footerPages', collect());
            }
        });
    }
}
