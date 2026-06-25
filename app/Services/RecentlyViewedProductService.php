<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Session\SessionManager;

class RecentlyViewedProductService
{
    private const SESSION_KEY = 'recently_viewed_products';
    private const LIMIT = 6;

    public function __construct(private SessionManager $session)
    {
    }

    public function record(Product $product): void
    {
        $ids = collect($this->session->get(self::SESSION_KEY, []))
            ->reject(fn ($id) => (int) $id === (int) $product->id)
            ->prepend($product->id)
            ->take(self::LIMIT)
            ->values()
            ->all();

        $this->session->put(self::SESSION_KEY, $ids);
    }

    public function get(?Product $excludeProduct = null): Collection
    {
        $ids = collect($this->session->get(self::SESSION_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($excludeProduct) {
            $ids = $ids->reject(fn ($id) => $id === (int) $excludeProduct->id);
        }

        $ids = $ids->take(self::LIMIT)->values();

        if ($ids->isEmpty()) {
            return new Collection();
        }

        return Product::active()
            ->with(['category', 'brand'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $product) => $ids->search($product->id))
            ->values();
    }
}
