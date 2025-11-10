<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionIndex extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterType = 'all';
    public $fromDate;
    public $toDate;

    public $totalIncome = 0;
    public $totalExpense = 0;

    public $isOpen = false;

    public $transactionId;
    public $type;
    public $title;
    public $amount;
    public $date;
    public $description;
    public $image;
    public $existingImage;

    public $chartLabels = [];
    public $chartIncome = [];
    public $chartExpense = [];

    protected $listeners = [
        'deleteConfirmed' => 'delete',
    ];

    public function mount()
    {
        $this->updateChartData();
    }

    // ======= SEARCH, FILTER, CHART =======
    public function updatedSearch()
    {
        $this->resetPage();
        $this->updateChartData();
        $this->dispatch('chart-updated');
    }

    public function updatedFilterType()
    {
        $this->resetPage();
        $this->updateChartData();
        $this->dispatch('chart-updated');
    }

    public function updatedFromDate()
    {
        $this->resetPage();
        $this->updateChartData();
        $this->dispatch('chart-updated');
    }

    public function updatedToDate()
    {
        $this->resetPage();
        $this->updateChartData();
        $this->dispatch('chart-updated');
    }

    // ======= FORM OPERATIONS =======
    public function openForm()
    {
        $this->resetForm();
        $this->isOpen = true;
    }

    public function closeForm()
    {
        $this->resetForm();
        $this->isOpen = false;
    }

    public function edit($id)
    {
        $tx = Transaction::where('user_id', Auth::id())->findOrFail($id);

        $this->transactionId = $tx->id;
        $this->type = $tx->type;
        $this->title = $tx->title;
        $this->amount = $tx->amount;
        $this->date = $tx->date->format('Y-m-d');
        $this->description = $tx->description;
        $this->existingImage = $tx->image;

        $this->isOpen = true;
    }

    // ======= DELETE LOGIC =======
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Konfirmasi Hapus',
            'text' => 'Apakah Anda yakin ingin menghapus data ini?',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya',
            'cancelButtonText' => 'Tidak',
            'onConfirmed' => 'deleteConfirmed',
            'onCancelled' => 'cancelDelete',
            'data' => $id,
        ]);
    }

    public function delete($id)
    {
        $tx = Transaction::where('user_id', Auth::id())->findOrFail($id);

        if ($tx->image && Storage::disk('public')->exists($tx->image)) {
            Storage::disk('public')->delete($tx->image);
        }

        $tx->delete();

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus',
        ]);

        $this->updateChartData();
        $this->dispatch('chart-updated');
        $this->resetPage();
    }

    public function cancelDelete()
    {
        $this->dispatch('swal:alert', [
            'type' => 'info',
            'message' => 'Penghapusan dibatalkan',
        ]);
    }

    // ======= SAVE =======
    public function save()
    {
        $this->validate([
            'type' => 'required|in:income,expense',
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'type' => $this->type,
            'title' => $this->title,
            'amount' => $this->amount,
            'date' => $this->date,
            'description' => $this->description,
        ];

        if ($this->image) {
            $imageName = time() . '_' . str_replace(' ', '_', $this->image->getClientOriginalName());
            $data['image'] = $this->image->storeAs('transactions', $imageName, 'public');

            if ($this->existingImage && Storage::disk('public')->exists($this->existingImage)) {
                Storage::disk('public')->delete($this->existingImage);
            }
        } elseif ($this->transactionId) {
            $tx = Transaction::find($this->transactionId);
            if ($tx && $tx->image) {
                $data['image'] = $tx->image;
            }
        }

        Transaction::updateOrCreate(['id' => $this->transactionId], $data);

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'message' => $this->transactionId ? 'Data berhasil diedit' : 'Data berhasil ditambah',
        ]);

        $this->closeForm();
        $this->updateChartData();
        $this->dispatch('chart-updated');
        $this->resetPage();
    }

    // ======= REMOVE IMAGE =======
    public function removeImage()
    {
        if ($this->existingImage && Storage::disk('public')->exists($this->existingImage)) {
            Storage::disk('public')->delete($this->existingImage);
        }

        $this->existingImage = null;
        $this->image = null;

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'message' => 'Bukti berhasil dihapus',
        ]);

        $this->updateChartData();
        $this->dispatch('chart-updated');
    }

    // ======= HELPER FUNCTIONS =======
    private function resetForm()
    {
        $this->transactionId = null;
        $this->type = '';
        $this->title = '';
        $this->amount = '';
        $this->date = '';
        $this->description = '';
        $this->image = null;
        $this->existingImage = null;
    }

    private function updateChartData()
    {
        $this->loadYearlyChart();
    }

    private function loadYearlyChart()
    {
        $monthly = Transaction::select(
                DB::raw("EXTRACT(MONTH FROM date) AS month"),
                DB::raw("SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS income"),
                DB::raw("SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS expense")
            )
            ->where('user_id', Auth::id())
            ->when($this->filterType !== 'all', fn($q) => $q->where('type', $this->filterType))
            ->when($this->fromDate, fn($q) => $q->whereDate('date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('date', '<=', $this->toDate))
            ->groupBy(DB::raw("EXTRACT(MONTH FROM date)"))
            ->orderBy(DB::raw("EXTRACT(MONTH FROM date)"))
            ->get()
            ->keyBy('month');

        $labels = [];
        $income = [];
        $expense = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create(2000, $m)->format('M');
            $income[] = $monthly[$m]->income ?? 0;
            $expense[] = $monthly[$m]->expense ?? 0;
        }

        $this->chartLabels = $labels;
        $this->chartIncome = $income;
        $this->chartExpense = $expense;
    }

    public function render()
    {
        $query = Transaction::where('user_id', Auth::id())
            ->when($this->search, function ($q) {
                $searchTerms = preg_split('/[\s,]+/', $this->search, -1, PREG_SPLIT_NO_EMPTY);

                return $q->where(function ($qq) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $term = trim($term);
                        if (!empty($term)) {
                            $qq->where(function ($qqq) use ($term) {
                                $qqq->where('title', 'like', '%' . $term . '%')
                                    ->orWhere('description', 'like', '%' . $term . '%')
                                    ->orWhere(DB::raw('CAST(amount AS TEXT)'), 'like', '%' . $term . '%')
                                    ->orWhere(DB::raw("TO_CHAR(date, 'DD FMMonth YYYY')"), 'like', '%' . $term . '%')
                                    ->orWhere(DB::raw("TO_CHAR(date, 'YYYY-MM-DD')"), 'like', '%' . $term . '%');
                            });
                        }
                    }
                });
            })
            ->when($this->filterType !== 'all', fn($q) => $q->where('type', $this->filterType))
            ->when($this->fromDate, fn($q) => $q->whereDate('date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('date', '<=', $this->toDate));

        $transactions = $query->orderBy('date', 'desc')->paginate(20);

        $this->totalIncome = Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->when($this->fromDate, fn($q) => $q->whereDate('date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('date', '<=', $this->toDate))
            ->sum('amount');

        $this->totalExpense = Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->when($this->fromDate, fn($q) => $q->whereDate('date', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('date', '<=', $this->toDate))
            ->sum('amount');

        return view('livewire.transaction-index', [
            'transactions' => $transactions,
            'chartLabels' => $this->chartLabels,
            'chartIncome' => $this->chartIncome,
            'chartExpense' => $this->chartExpense,
        ]);
    }
}
