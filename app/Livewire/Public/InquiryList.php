<?php

namespace App\Livewire\Public;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class InquiryList extends Component
{
    /**
     * @return list<array{key: string, product_id: int, product_name: string, product_slug: string, product_description: string|null, product_image_path: string|null, feature_value: string|null, price: float, vat_rate: float, quantity: int, line_net: float, line_vat: float, line_gross: float}>
     */
    public function items(): array
    {
        $sessionItems = collect(session('inquiry_list.items', []));

        if ($sessionItems->isEmpty()) {
            return [];
        }

        $products = Product::query()
            ->whereIn('id', $sessionItems->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        return $sessionItems
            ->map(function (array $item) use ($products): ?array {
                $product = $products->get((int) $item['product_id']);

                if (! $product) {
                    return null;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $price = (float) $product->price;
                $vatRate = (float) ($product->vat_rate ?? 19.0);
                $lineNet = $price * $quantity;
                $lineVat = $lineNet * ($vatRate / 100);
                $lineGross = $lineNet + $lineVat;

                return [
                    'key' => (string) $item['key'],
                    'product_id' => (int) $product->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_description' => $product->description,
                    'product_image_path' => $product->image_path,
                    'feature_value' => $item['feature_value'] ?: null,
                    'price' => $price,
                    'vat_rate' => $vatRate,
                    'quantity' => $quantity,
                    'line_net' => $lineNet,
                    'line_vat' => $lineVat,
                    'line_gross' => $lineGross,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function decreaseQuantity(string $key): void
    {
        $item = $this->findItemByKey($key);

        if (! $item) {
            return;
        }

        $newQuantity = max(1, (int) $item['quantity'] - 1);
        $this->updateQuantity($key, $newQuantity);
    }

    public function increaseQuantity(string $key): void
    {
        $item = $this->findItemByKey($key);

        if (! $item) {
            return;
        }

        $this->updateQuantity($key, (int) $item['quantity'] + 1);
    }

    public function updateQuantity(string $key, int $quantity): void
    {
        $items = collect(session('inquiry_list.items', []))
            ->map(function (array $item) use ($key, $quantity): array {
                if ((string) $item['key'] === $key) {
                    $item['quantity'] = max(1, $quantity);
                }

                return $item;
            })
            ->values()
            ->all();

        session()->put('inquiry_list.items', $items);
        $this->dispatch('inquiry-list-updated');
    }

    public function removeItem(string $key): void
    {
        $items = collect(session('inquiry_list.items', []))
            ->reject(fn (array $item): bool => (string) $item['key'] === $key)
            ->values()
            ->all();

        session()->put('inquiry_list.items', $items);
        $this->dispatch('inquiry-list-updated');

        Flux::toast(__('Position wurde entfernt.'));
    }

    /**
     * @return array{
     *     subtotal_net: float,
     *     vat_total: float,
     *     grand_total: float,
     *     vat_breakdown: list<array{rate: float, amount: float}>
     * }
     */
    public function getSummaryProperty(): array
    {
        $items = Collection::make($this->items());
        $vatBreakdown = $items
            ->groupBy(fn (array $item): string => (string) $item['vat_rate'])
            ->map(function (Collection $groupedItems, string $rate): array {
                return [
                    'rate' => (float) $rate,
                    'amount' => (float) $groupedItems->sum('line_vat'),
                ];
            })
            ->sortByDesc('rate')
            ->values()
            ->all();

        return [
            'subtotal_net' => (float) $items->sum('line_net'),
            'vat_total' => (float) $items->sum('line_vat'),
            'grand_total' => (float) $items->sum('line_gross'),
            'vat_breakdown' => $vatBreakdown,
        ];
    }

    /**
     * @return array{key: string, product_id: int, feature_value: string, quantity: int}|null
     */
    private function findItemByKey(string $key): ?array
    {
        return collect(session('inquiry_list.items', []))
            ->first(fn (array $item): bool => (string) $item['key'] === $key);
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.inquiry-list', [
            'items' => $this->items(),
            'summary' => $this->summary,
        ]);
    }
}
