<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\InquiryItem;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Products extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $categoryFilter = '';

    public ?Product $product = null;

    public $category_id = '';

    public $name = '';

    public $slug = '';

    public $description = '';

    public $keywords = '';

    public $price = '';

    public $vat_rate = '19';

    public $image = null;

    public $feature_name = '';

    public $feature_values = '';

    public $editing = false;

    public ?Product $inquiryHistoryProduct = null;

    /**
     * @var array<int, array{
     *     inquiry_id: int,
     *     inquiry_date: string,
     *     customer_name: string,
     *     customer_company: ?string,
     *     quantity: int
     * }>
     */
    public array $productInquiries = [];

    public function updatedName($value)
    {
        if (! $this->editing) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->reset(['category_id', 'name', 'slug', 'description', 'keywords', 'price', 'vat_rate', 'image', 'feature_name', 'feature_values', 'editing', 'product']);
        $this->vat_rate = '19';
        $this->slug = '';
        $this->dispatch('modal-show', name: 'product-modal');
    }

    public function edit(Product $product)
    {
        $this->product = $product;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->keywords = is_array($product->keywords) ? implode(', ', $product->keywords) : $product->keywords;
        $this->price = $product->price;
        $this->vat_rate = (string) $product->vat_rate;
        $this->feature_name = $product->feature_name;
        $this->feature_values = is_array($product->feature_values) ? implode(', ', $product->feature_values) : $product->feature_values;

        $this->editing = true;
        $this->image = null;
        $this->dispatch('modal-show', name: 'product-modal');
    }

    public function save()
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|min:3|max:255',
            'slug' => 'required|min:3|max:255|unique:products,slug'.($this->editing ? ','.$this->product->id : ''),
            'description' => 'nullable',
            'keywords' => 'nullable',
            'price' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:99.99',
            'image' => 'nullable|image|max:2048',
            'feature_name' => 'nullable|max:255',
            'feature_values' => 'nullable',
        ];

        $this->validate($rules);

        $keywordsArray = $this->keywords ? array_map('trim', explode(',', $this->keywords)) : [];
        $featureValuesArray = $this->feature_values ? array_map('trim', explode(',', $this->feature_values)) : [];

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'keywords' => $keywordsArray,
            'price' => $this->price,
            'vat_rate' => $this->vat_rate,
            'feature_name' => $this->feature_name,
            'feature_values' => $featureValuesArray,
        ];

        if ($this->image) {
            if ($this->editing && $this->product->image_path && ! $this->product->inquiries()->exists()) {
                Storage::disk('public')->delete($this->product->image_path);
            }
            $data['image_path'] = $this->image->store('products', 'public');
        }

        if ($this->editing) {
            if ($this->product->inquiries()->exists()) {
                DB::transaction(function () use ($data): void {
                    $originalSlug = $this->product->slug;

                    $this->product->update([
                        'slug' => $this->archivedSlug($originalSlug, $this->product->id),
                    ]);

                    $this->product->delete();

                    Product::create(array_merge($data, [
                        'slug' => $originalSlug,
                        'supersedes_product_id' => $this->product->id,
                    ]));
                });
            } else {
                $this->product->update($data);
            }
            Flux::toast(__('Produkt wurde aktualisiert.'));
        } else {
            Product::create($data);
            Flux::toast(__('Produkt wurde erstellt.'));
        }

        $this->dispatch('modal-close', name: 'product-modal');
        $this->reset(['category_id', 'name', 'slug', 'description', 'keywords', 'price', 'vat_rate', 'image', 'feature_name', 'feature_values', 'editing', 'product']);
        $this->vat_rate = '19';
    }

    public function delete(Product $product)
    {
        $this->product = $product;
        $this->dispatch('modal-show', name: 'delete-confirmation');
    }

    public function showInquiries(Product $product): void
    {
        $this->inquiryHistoryProduct = $product;

        $productIds = $this->resolveProductFamilyIds($product->id);

        $this->productInquiries = InquiryItem::query()
            ->with('inquiry')
            ->whereIn('product_id', $productIds)
            ->get()
            ->sortByDesc(fn (InquiryItem $inquiryItem): int => $inquiryItem->inquiry?->created_at?->getTimestamp() ?? 0)
            ->map(function (InquiryItem $inquiryItem): ?array {
                if ($inquiryItem->inquiry === null) {
                    return null;
                }

                return [
                    'inquiry_id' => $inquiryItem->inquiry->id,
                    'inquiry_date' => $inquiryItem->inquiry->created_at->format('d.m.Y H:i'),
                    'customer_name' => $this->formatCustomerName($inquiryItem->inquiry->first_name, $inquiryItem->inquiry->last_name),
                    'customer_company' => $inquiryItem->inquiry->company,
                    'quantity' => (int) $inquiryItem->quantity,
                ];
            })
            ->filter()
            ->values()
            ->all();

        $this->dispatch('modal-show', name: 'product-inquiries-modal');
    }

    public function confirmDelete()
    {
        if ($this->product->image_path && ! $this->product->inquiries()->exists()) {
            Storage::disk('public')->delete($this->product->image_path);
        }

        $this->product->delete();
        Flux::toast(__('Produkt wurde gelöscht.'));

        $this->dispatch('modal-close', name: 'delete-confirmation');
        $this->reset(['product']);
    }

    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.products', [
            'products' => Product::query()
                ->with('category')
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->when($this->categoryFilter, fn ($query) => $query->where('category_id', $this->categoryFilter))
                ->latest()
                ->paginate(10),
        ]);
    }

    private function archivedSlug(string $slug, int $productId): string
    {
        return Str::limit($slug.'-archived-'.$productId, 255, '');
    }

    /**
     * @return array<int, int>
     */
    private function resolveProductFamilyIds(int $productId): array
    {
        $productIds = [$productId];
        $uncheckedIds = [$productId];

        while ($uncheckedIds !== []) {
            $relatedProductIds = Product::withTrashed()
                ->whereIn('id', $uncheckedIds)
                ->orWhereIn('supersedes_product_id', $uncheckedIds)
                ->pluck('id')
                ->merge(
                    Product::withTrashed()
                        ->whereIn('id', $uncheckedIds)
                        ->pluck('supersedes_product_id')
                )
                ->filter(fn ($id): bool => $id !== null)
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values();

            $newIds = $relatedProductIds
                ->diff($productIds)
                ->values()
                ->all();

            $productIds = array_values(array_unique(array_merge($productIds, $newIds)));
            $uncheckedIds = $newIds;
        }

        return $productIds;
    }

    private function formatCustomerName(string $firstName, string $lastName): string
    {
        return trim($firstName.' '.$lastName);
    }
}
