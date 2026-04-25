<?php

namespace App\Livewire\Admin;

use App\Models\Inquiry;
use App\Models\InquiryItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * @var array<string, string>
     */
    private const STATUS_LABELS = [
        'new' => 'Neu',
        'in_progress' => 'In Bearbeitung',
        'quote_created' => 'Angebot erstellt',
        'completed' => 'Abgeschlossen',
        'cancelled' => 'Storniert',
    ];

    /**
     * @var list<string>
     */
    private const ACTIVE_STATUSES = ['new', 'in_progress', 'quote_created'];

    /**
     * @return array<string, mixed>
     */
    private function metrics(): array
    {
        $today = now();
        $last24Hours = $today->copy()->subDay();
        $last30Days = $today->copy()->subDays(30);
        $last90Days = $today->copy()->subDays(90);

        $newInquiries24h = Inquiry::query()
            ->where('status', 'new')
            ->where('created_at', '>=', $last24Hours)
            ->count();

        $activeInquiryCount = Inquiry::query()
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->count();

        /** @var array<string, int> $activeStatuses */
        $activeStatuses = Inquiry::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn (int $count): int => (int) $count)
            ->all();

        $recentInquiries = Inquiry::query()
            ->withCount('products')
            ->latest()
            ->limit(8)
            ->get();

        /** @var Collection<int, Inquiry> $monthlyInquiriesRaw */
        $monthlyInquiriesRaw = Inquiry::query()
            ->select(['id', 'created_at'])
            ->where('created_at', '>=', now()->startOfMonth()->subMonths(11))
            ->get();

        $monthlyInquiries = $this->fillMissingMonths($monthlyInquiriesRaw);

        $completedRevenue = $this->revenueForStatuses(['completed']);
        $openQuoteVolume = $this->revenueForStatuses(['quote_created']);

        $revenueToday = $this->revenueForStatuses(['completed'], now()->startOfDay());
        $revenueMonth = $this->revenueForStatuses(['completed'], now()->startOfMonth());

        $completedInquiriesCount = Inquiry::query()
            ->where('status', 'completed')
            ->count();

        $averageOrderValue = $completedInquiriesCount > 0
            ? round($completedRevenue / $completedInquiriesCount, 2)
            : 0.0;

        $durations = Inquiry::query()
            ->where('status', 'completed')
            ->where('created_at', '>=', $last90Days)
            ->get(['created_at', 'updated_at'])
            ->map(function (Inquiry $inquiry): int {
                return Carbon::parse($inquiry->created_at)->diffInDays(Carbon::parse($inquiry->updated_at));
            })
            ->sort()
            ->values();

        $medianCompletionDays = $this->median($durations->all());

        $topProducts = InquiryItem::query()
            ->join('products', 'products.id', '=', 'inquiry_items.product_id')
            ->join('inquiries', 'inquiries.id', '=', 'inquiry_items.inquiry_id')
            ->where('inquiries.created_at', '>=', $last30Days)
            ->groupBy('inquiry_items.product_id', 'products.name')
            ->selectRaw('products.name as product_name')
            ->selectRaw('COUNT(DISTINCT inquiry_items.inquiry_id) as inquiry_count')
            ->selectRaw('SUM(inquiry_items.quantity) as total_quantity')
            ->orderByDesc('inquiry_count')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $inactiveProducts = Product::query()
            ->leftJoin('inquiry_items', 'inquiry_items.product_id', '=', 'products.id')
            ->leftJoin('inquiries', function ($join) use ($last90Days): void {
                $join->on('inquiries.id', '=', 'inquiry_items.inquiry_id')
                    ->where('inquiries.created_at', '>=', $last90Days);
            })
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'products.name')
            ->havingRaw('COUNT(inquiries.id) = 0')
            ->select('products.name')
            ->orderBy('products.name')
            ->limit(10)
            ->pluck('name');

        $monthlyRevenue = $this->monthlyRevenue();
        $yearToDateRevenue = array_sum(array_map(
            static fn (array $entry): float => $entry['amount'],
            array_values($monthlyRevenue)
        ));

        return [
            'newInquiries24h' => $newInquiries24h,
            'activeInquiryCount' => $activeInquiryCount,
            'activeStatuses' => $activeStatuses,
            'recentInquiries' => $recentInquiries,
            'monthlyInquiries' => $monthlyInquiries,
            'medianCompletionDays' => $medianCompletionDays,
            'topProducts' => $topProducts,
            'inactiveProducts' => $inactiveProducts,
            'revenueToday' => $revenueToday,
            'revenueMonth' => $revenueMonth,
            'yearToDateRevenue' => $yearToDateRevenue,
            'averageOrderValue' => $averageOrderValue,
            'openQuoteVolume' => $openQuoteVolume,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueDefinition' => 'Umsatz = Summe aus Produktpreis x Menge fuer Inquiry-Items mit Inquiry-Status "Abgeschlossen".',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function statusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    public function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
    }

    /**
     * @param  Collection<int, Inquiry>  $rows
     * @return list<array{month: string, count: int}>
     */
    private function fillMissingMonths(Collection $rows): array
    {
        /** @var array<string, int> $countsByMonth */
        $countsByMonth = $rows
            ->groupBy(fn (Inquiry $inquiry): string => Carbon::parse($inquiry->created_at)->format('Y-m-01'))
            ->map(fn (Collection $group): int => $group->count())
            ->all();

        $monthCursor = now()->startOfMonth()->subMonths(11);
        $result = [];

        for ($i = 0; $i < 12; $i++) {
            $monthKey = $monthCursor->format('Y-m-01');

            $result[] = [
                'month' => $monthCursor->format('m/Y'),
                'count' => (int) ($countsByMonth[$monthKey] ?? 0),
            ];

            $monthCursor = $monthCursor->addMonth();
        }

        return array_reverse($result);
    }

    /**
     * @param  list<string>  $statuses
     */
    private function revenueForStatuses(array $statuses, ?\DateTimeInterface $from = null): float
    {
        $query = InquiryItem::query()
            ->join('products', 'products.id', '=', 'inquiry_items.product_id')
            ->join('inquiries', 'inquiries.id', '=', 'inquiry_items.inquiry_id')
            ->whereIn('inquiries.status', $statuses)
            ->selectRaw('COALESCE(SUM(inquiry_items.quantity * products.price), 0) as total');

        if ($from !== null) {
            $query->where('inquiries.created_at', '>=', $from);
        }

        return (float) $query->value('total');
    }

    /**
     * @return list<array{month: string, amount: float}>
     */
    private function monthlyRevenue(): array
    {
        /** @var Collection<int, object{created_at: string, line_amount: float}> $rows */
        $rows = InquiryItem::query()
            ->join('products', 'products.id', '=', 'inquiry_items.product_id')
            ->join('inquiries', 'inquiries.id', '=', 'inquiry_items.inquiry_id')
            ->where('inquiries.status', 'completed')
            ->where('inquiries.created_at', '>=', now()->startOfMonth()->subMonths(11))
            ->selectRaw('inquiries.created_at as created_at')
            ->selectRaw('(inquiry_items.quantity * products.price) as line_amount')
            ->get();

        /** @var array<string, float> $amountsByMonth */
        $amountsByMonth = $rows
            ->groupBy(fn (object $row): string => Carbon::parse($row->created_at)->format('Y-m-01'))
            ->map(fn (Collection $group): float => (float) $group->sum('line_amount'))
            ->all();

        $monthCursor = now()->startOfMonth()->subMonths(11);
        $result = [];

        for ($i = 0; $i < 12; $i++) {
            $monthKey = $monthCursor->format('Y-m-01');

            $result[] = [
                'month' => $monthCursor->format('m/Y'),
                'amount' => round((float) ($amountsByMonth[$monthKey] ?? 0), 2),
            ];

            $monthCursor = $monthCursor->addMonth();
        }

        return array_reverse($result);
    }

    /**
     * @param  list<int>  $values
     */
    private function median(array $values): float
    {
        $count = count($values);

        if ($count === 0) {
            return 0.0;
        }

        sort($values);

        $middle = intdiv($count, 2);

        if ($count % 2 === 1) {
            return (float) $values[$middle];
        }

        return round(($values[$middle - 1] + $values[$middle]) / 2, 1);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'metrics' => $this->metrics(),
        ]);
    }
}
