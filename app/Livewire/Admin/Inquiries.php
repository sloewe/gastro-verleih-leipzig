<?php

namespace App\Livewire\Admin;

use App\Models\Inquiry;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Inquiries extends Component
{
    use WithPagination;

    private bool $shouldOpenDetailsModal = false;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    #[Url(as: 'inquiry')]
    public ?int $selectedInquiryId = null;

    /**
     * @var array<string, string>
     */
    private const STATUSES = [
        'new' => 'Neu',
        'in_progress' => 'In Bearbeitung',
        'quote_created' => 'Angebot erstellt',
        'completed' => 'Abgeschlossen',
        'cancelled' => 'Storniert',
    ];

    /**
     * @var list<string>
     */
    private const SORTABLE_FIELDS = [
        'id',
        'created_at',
        'start_date',
        'first_name',
        'email',
        'status',
    ];

    public function mount(): void
    {
        if ($this->selectedInquiryId !== null) {
            $inquiryExists = Inquiry::query()->whereKey($this->selectedInquiryId)->exists();

            if (! $inquiryExists) {
                $this->selectedInquiryId = null;
            } else {
                $this->shouldOpenDetailsModal = true;
            }
        }

        if ($this->selectedInquiryId === null) {
            $this->selectedInquiryId = Inquiry::query()->latest('created_at')->value('id');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        return self::STATUSES;
    }

    public function selectInquiry(int $inquiryId): void
    {
        $this->selectedInquiryId = $inquiryId;

        $this->dispatch('modal-show', name: 'inquiry-details-modal');
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    public function rendered(): void
    {
        if (! $this->shouldOpenDetailsModal) {
            return;
        }

        $this->dispatch('modal-show', name: 'inquiry-details-modal');
        $this->shouldOpenDetailsModal = false;
    }

    public function updateStatus(int $inquiryId, string $status): void
    {
        validator(
            ['status' => $status],
            ['status' => ['required', 'string', Rule::in(array_keys($this->statusOptions()))]]
        )->validate();

        $inquiry = Inquiry::query()->findOrFail($inquiryId);
        $inquiry->update(['status' => $status]);

        $this->selectedInquiryId = $inquiry->id;

        Flux::toast(__('Status wurde aktualisiert.'));
    }

    public function statusLabel(string $status): string
    {
        return $this->statusOptions()[$status] ?? $status;
    }

    public function statusBadgeVariant(string $status): string
    {
        return match ($status) {
            'completed' => 'success',
            'cancelled' => 'danger',
            'quote_created' => 'primary',
            'in_progress' => 'warning',
            default => 'outline',
        };
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $inquiries = Inquiry::query()
            ->with('products')
            ->when(
                $this->search !== '',
                fn ($query) => $query
                    ->where('first_name', 'like', '%'.$this->search.'%')
                    ->orWhere('last_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('company', 'like', '%'.$this->search.'%')
            )
            ->when(
                $this->statusFilter !== '',
                fn ($query) => $query->where('status', $this->statusFilter)
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $selectedInquiry = null;

        if ($this->selectedInquiryId !== null) {
            $selectedInquiry = Inquiry::query()
                ->with('products')
                ->find($this->selectedInquiryId);
        }

        if ($selectedInquiry === null && $inquiries->isNotEmpty()) {
            $selectedInquiry = $inquiries->first();
            $this->selectedInquiryId = $selectedInquiry?->id;
        }

        return view('livewire.admin.inquiries', [
            'inquiries' => $inquiries,
            'selectedInquiry' => $selectedInquiry,
        ]);
    }
}
