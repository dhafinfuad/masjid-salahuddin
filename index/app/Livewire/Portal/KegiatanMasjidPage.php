<?php

namespace App\Livewire\Portal;

use App\Models\Agenda;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kegiatan & Jadwal Masjid — Masjid Salahuddin')]
class KegiatanMasjidPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = 'all'; // 'all', 'pekanan', 'tematik', 'jumat', 'akbar'
    public string $selectedMonth = 'all'; // 'all' or 1..12
    public int $selectedYear = 2026;

    public function mount(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        // Default to current month or 'all'
        $this->selectedMonth = (string) $now->month;
        $this->selectedYear = (int) $now->year;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $this->selectedCategory = $category;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
        $this->selectedMonth = 'all';
        $this->selectedYear = (int) Carbon::now('Asia/Jakarta')->year;
        $this->resetPage();
    }

    public function render()
    {
        $settings = MasjidSetting::getActive();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $years = [2026, 2027];

        $searchTerm = trim($this->search);
        $monthFilter = $this->selectedMonth;
        $yearFilter = $this->selectedYear;
        $categoryFilter = $this->selectedCategory;

        $items = collect();

        // 1. Query Kajian (Pekanan, Tematik, Khutbah Jumat) jika kategori mengizinkan
        if ($categoryFilter !== 'akbar') {
            Kajian::ensureNotulaColumnExists();
            Kajian::ensureYoutubeColumnExists();
            $kajianQuery = Kajian::query();

            if ($monthFilter !== 'all' && is_numeric($monthFilter)) {
                $kajianQuery->whereMonth('date', (int) $monthFilter);
            }

            if (!empty($yearFilter)) {
                $kajianQuery->whereYear('date', (int) $yearFilter);
            }

            if ($categoryFilter === 'jumat') {
                $kajianQuery->where('type', 'jumat');
            } elseif ($categoryFilter === 'tematik') {
                $kajianQuery->where('type', 'tematik');
            } elseif ($categoryFilter === 'pekanan') {
                $kajianQuery->where('type', 'pekanan');
            }

            if (!empty($searchTerm)) {
                $kajianQuery->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('speaker_name', 'like', "%{$searchTerm}%")
                      ->orWhere('khatib_name', 'like', "%{$searchTerm}%")
                      ->orWhere('muadzin_name', 'like', "%{$searchTerm}%")
                      ->orWhere('mc_name', 'like', "%{$searchTerm}%");
                });
            }

            $kajianResults = $kajianQuery->orderBy('date', 'asc')->get()->map(function ($k) {
                return [
                    'source' => 'kajian',
                    'id' => $k->id,
                    'type' => $k->type,
                    'date' => $k->date,
                    'time_display' => $k->time_display,
                    'title' => $k->title,
                    'speaker_name' => $k->speaker_name,
                    'khatib_name' => $k->khatib_name,
                    'muadzin_name' => $k->muadzin_name,
                    'mc_name' => $k->mc_name,
                    'location' => $k->location,
                    'description' => $k->description,
                    'status' => $k->status,
                    'notula' => $k->notula,
                    'youtube_url' => $k->youtube_url,
                ];
            });

            $items = $items->concat($kajianResults);
        }

        // 2. Query Agenda (Kegiatan Akbar) jika kategori mengizinkan
        if ($categoryFilter === 'all' || $categoryFilter === 'akbar') {
            Agenda::ensureYoutubeColumnExists();
            $agendaQuery = Agenda::query();

            if ($monthFilter !== 'all' && is_numeric($monthFilter)) {
                $agendaQuery->whereMonth('event_date', (int) $monthFilter);
            }

            if (!empty($yearFilter)) {
                $agendaQuery->whereYear('event_date', (int) $yearFilter);
            }

            if (!empty($searchTerm)) {
                $agendaQuery->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            $agendaResults = $agendaQuery->orderBy('event_date', 'asc')->get()->map(function ($a) {
                return [
                    'source' => 'agenda',
                    'id' => $a->id,
                    'type' => 'akbar',
                    'date' => $a->event_date,
                    'time_display' => 'Sesuai Jadwal',
                    'title' => $a->title,
                    'speaker_name' => null,
                    'khatib_name' => null,
                    'muadzin_name' => null,
                    'mc_name' => null,
                    'location' => 'Masjid Salahuddin',
                    'description' => $a->description,
                    'budget' => $a->budget,
                    'status' => $a->status ?: 'Direncanakan',
                    'youtube_url' => $a->youtube_url,
                ];
            });

            $items = $items->concat($agendaResults);
        }

        // Sort items by date ascending
        $sortedItems = $items->sortBy(function ($item) {
            return $item['date'] ? $item['date']->format('Y-m-d') : '9999-12-31';
        })->values();

        $totalCount = $sortedItems->count();

        // Paginate manually with standard 12 items per page
        $perPage = 12;
        $currentPage = $this->getPage();
        $pagedData = $sortedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedItems = new LengthAwarePaginator(
            $pagedData,
            $totalCount,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->format('Y-m-d');
        $kajianCurrentWeekEnd = $now->copy()->endOfWeek()->format('Y-m-d');

        $nextPekananKajianId = Kajian::kajianUmum()
            ->whereDate('date', '>=', $todayDate)
            ->orderBy('date', 'asc')
            ->value('id');

        $nextJumatKajianId = Kajian::jumat()
            ->whereDate('date', '>=', $todayDate)
            ->orderBy('date', 'asc')
            ->value('id');

        $nextAgendaId = Agenda::whereDate('event_date', '>=', $todayDate)
            ->orderBy('event_date', 'asc')
            ->value('id');

        return view('livewire.portal.kegiatan-masjid-page', [
            'settings' => $settings,
            'items' => $paginatedItems,
            'totalCount' => $totalCount,
            'months' => $months,
            'years' => $years,
            'todayDate' => $todayDate,
            'kajianCurrentWeekEnd' => $kajianCurrentWeekEnd,
            'nextPekananKajianId' => $nextPekananKajianId,
            'nextJumatKajianId' => $nextJumatKajianId,
            'nextAgendaId' => $nextAgendaId,
        ]);
    }
}
