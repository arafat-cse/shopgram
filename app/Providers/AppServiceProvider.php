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
use Illuminate\Support\Facades\Cache;
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

        // Resolved lazily on first use, once per request — the '*' composer
        // fires for every view/component render and must not re-run queries.
        $this->app->singleton('view.global_data', function () {
            $data = [
                'cartCount'         => 0,
                'wishlistCount'     => 0,
                'siteName'          => 'ShopGram',
                'siteLogo'          => null,
                'currencySymbol'    => '৳',
                'coinSystemEnabled' => coin_system_enabled(),
                'navCategories'     => collect(),
                'footerPages'       => collect(),
            ];

            if ($user = auth()->user()) {
                $data['cartCount'] = app(CartService::class)->getCount($user);
                $data['wishlistCount'] = app(WishlistService::class)->getCount($user);
            }

            try {
                $data['siteName'] = Setting::get('site_name', 'ShopGram');
                $data['siteLogo'] = Setting::get('site_logo');
                $data['currencySymbol'] = Setting::get('currency_symbol', '৳');
            } catch (\Exception $e) {
            }

            try {
                $data['navCategories'] = Cache::remember('nav_categories', 1800, fn() => Category::active()
                    ->parent()
                    ->with(['children' => fn($query) => $query->active()->orderBy('name')])
                    ->orderBy('name')
                    ->get());
            } catch (\Exception $e) {
            }

            try {
                $data['footerPages'] = Cache::remember('footer_pages', 1800, fn() => Page::active()->footer()->orderBy('title')->get());
            } catch (\Exception $e) {
            }

            return $data;
        });

        View::composer('*', fn ($view) => $view->with(app()->make('view.global_data')));
    }
}
