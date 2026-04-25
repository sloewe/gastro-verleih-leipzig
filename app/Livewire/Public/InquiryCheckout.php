<?php

namespace App\Livewire\Public;

use App\Mail\InquiryAdminNotificationMail;
use App\Mail\InquiryCustomerConfirmationMail;
use App\Models\Inquiry;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class InquiryCheckout extends Component
{
    public string $salutation = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public string $street = '';

    public string $postal_code = '';

    public string $city = '';

    public string $start_date = '';

    public string $end_date = '';

    public string $message = '';

    public function mount(): void
    {
        if (empty(session('inquiry_list.items', []))) {
            session()->flash('checkout_error', __('Ihre Anfrageliste ist leer.'));
            $this->redirectRoute('inquiry.list', navigate: true);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'salutation' => ['required', Rule::in(['Herr', 'Frau', 'Divers'])],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:32'],
            'city' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'message' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate();
        $resolvedItems = $this->resolvedSessionItems();

        if (empty($resolvedItems)) {
            session()->flash('checkout_error', __('Ihre Anfrageliste ist leer oder enthaelt keine gueltigen Produkte.'));
            $this->redirectRoute('inquiry.list', navigate: true);

            return;
        }

        $inquiry = DB::transaction(function () use ($validated, $resolvedItems): Inquiry {
            $inquiry = Inquiry::query()->create([
                'salutation' => $validated['salutation'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'company' => $validated['company'] ?: null,
                'street' => $validated['street'],
                'postal_code' => $validated['postal_code'],
                'city' => $validated['city'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'address' => trim($validated['street'].', '.$validated['postal_code'].' '.$validated['city']),
                'message' => $validated['message'] ?: null,
                'status' => 'new',
            ]);

            $attachData = [];

            foreach ($resolvedItems as $item) {
                $attachData[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'feature_value' => $item['feature_value'] ?: null,
                ];
            }

            $inquiry->products()->attach($attachData);

            return $inquiry->load('products');
        });

        Mail::to($inquiry->email)->send(new InquiryCustomerConfirmationMail($inquiry));
        Mail::to((string) config('mail.inquiry_admin_address', config('mail.from.address')))
            ->send(new InquiryAdminNotificationMail($inquiry));

        session()->forget('inquiry_list.items');
        $this->dispatch('inquiry-list-updated');

        $this->redirectRoute('inquiry.thank-you', navigate: true);
    }

    /**
     * @return list<array{
     *     key: string,
     *     product_id: int,
     *     product_name: string,
     *     quantity: int,
     *     feature_value: string,
     *     vat_rate: float,
     *     line_net: float,
     *     line_vat: float,
     *     line_gross: float
     * }>
     */
    public function cartItems(): array
    {
        return $this->resolvedSessionItems();
    }

    /**
     * @return array{subtotal_net: float, subtotal_vat: float, subtotal_gross: float}
     */
    public function getSummaryProperty(): array
    {
        $resolvedItems = collect($this->resolvedSessionItems());
        $subtotalNet = (float) $resolvedItems->sum('line_net');
        $subtotalVat = (float) $resolvedItems->sum('line_vat');

        return [
            'subtotal_net' => $subtotalNet,
            'subtotal_vat' => $subtotalVat,
            'subtotal_gross' => $subtotalNet + $subtotalVat,
        ];
    }

    /**
     * @return list<array{
     *     key: string,
     *     product_id: int,
     *     product_name: string,
     *     quantity: int,
     *     feature_value: string,
     *     vat_rate: float,
     *     line_net: float,
     *     line_vat: float,
     *     line_gross: float
     * }>
     */
    private function resolvedSessionItems(): array
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
                $vatRate = (float) $product->vat_rate;
                $lineNet = $price * $quantity;
                $lineVat = $lineNet * ($vatRate / 100);

                return [
                    'key' => (string) $item['key'],
                    'product_id' => (int) $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'feature_value' => (string) ($item['feature_value'] ?? ''),
                    'vat_rate' => $vatRate,
                    'line_net' => $lineNet,
                    'line_vat' => $lineVat,
                    'line_gross' => $lineNet + $lineVat,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public.inquiry-checkout', [
            'items' => $this->cartItems(),
            'summary' => $this->summary,
        ]);
    }
}
