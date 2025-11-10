<div class="p-4">
    <div class="p-3 bg-light min-vh-100">

        <div class="mb-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <h2 class="h4 fw-bold text-dark">Catatan Keuangan</h2>
                <p class="text-muted mb-0">Ringkasan keuangan Anda</p>
            </div>

            <button type="button" wire:click="openForm"
                class="btn btn-primary">
                + Tambah Transaksi
            </button>
        </div>

        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="card text-dark bg-success mb-0">
                    <div class="card-body text-center">
                        <h6 class="text-white text-uppercase small">Pemasukan</h6>
                        <div class="h4 fw-bold mt-2">@currency($totalIncome)</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-dark bg-danger mb-0">
                    <div class="card-body text-center">
                        <h6 class="text-white text-uppercase small">Pengeluaran</h6>
                        <div class="h4 fw-bold mt-2">@currency($totalExpense)</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-dark bg-primary mb-0">
                    <div class="card-body text-center">
                        <h6 class="text-white text-uppercase small">Total Saldo</h6>
                        <div class="h4 fw-bold mt-2">@currency($totalIncome - $totalExpense)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Statistik Transaksi</h5>
                <div id="transactionsChart" wire:ignore style="min-height:320px;"></div>

                <div id="chart-data" style="display:none;">
                    {!! json_encode([
                    'labels' => (array) $chartLabels,
                    'income' => (array) $chartIncome,
                    'expense' => (array) $chartExpense,
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">

                <div class="row mb-3 g-2 align-items-center">
                    <div class="col-md-4 position-relative">
                        <label class="visually-hidden" for="search_tx">Cari transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input 
                                wire:model.live.debounce.300ms="search" 
                                type="text" 
                                placeholder="Cari transaksi (judul, tanggal, jumlah)..." 
                                class="form-control"
                                id="search_tx"
                                name="search"
                            >
                            @if($search)
                                <button wire:click="$set('search', '')" class="btn btn-outline-secondary" type="button" aria-label="Hapus pencarian">
                                    &times;
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-8 d-flex flex-wrap align-items-center gap-2">
                        <select wire:model.live="filterType" class="form-select me-2" id="filter_type" name="filter_type">
                            <option value="all">Semua</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>

                        <label class="me-1">Dari</label>
                        <input wire:model.live="fromDate" type="date" class="form-control me-2" id="filter_from_date" name="from_date" style="max-width:170px;">

                        <label class="me-1">Sampai</label>
                        <input wire:model.live="toDate" type="date" class="form-control" id="filter_to_date" name="to_date" style="max-width:170px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Bukti</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                            <tr>
                                <td>{{ $tx->date->format('d M Y') }}</td>
                                <td>{{ $tx->title }}</td>
                                <td>
                                    <span class="badge {{ $tx->type == 'income' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $tx->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $tx->type == 'income' ? '+' : '-' }} @currency($tx->amount)
                                </td>
                                <td>
                                    @if($tx->image)
                                    <a target="_blank" href="{{ asset('storage/'.$tx->image) }}" class="link-primary">Lihat</a>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="edit({{ $tx->id }})" class="btn btn-sm btn-warning me-1">Edit</button>

                                    <!-- use confirmDelete to trigger SweetAlert confirmation -->
                                    <button wire:click="confirmDelete({{ $tx->id }})" type="button" class="btn btn-sm btn-danger">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>

            </div>
        </div>

    </div>

    @if($isOpen)
    <div wire:key="transaction-form-modal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $transactionId ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h5>
                    <button type="button" class="btn-close" aria-label="Close" wire:click="closeForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save" class="row g-3">
                        <div class="col-md-6">
                            <label for="tx_type" class="form-label">Jenis Transaksi</label>
                            <select wire:model="type" id="tx_type" name="type" class="form-select">
                                <option value="">Pilih jenis</option>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                            @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tx_date" class="form-label">Tanggal</label>
                            <input type="date" wire:model="date" id="tx_date" name="date" class="form-control">
                            @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="tx_title" class="form-label">Judul</label>
                            <input type="text" wire:model="title" id="tx_title" name="title" class="form-control">
                            @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="tx_amount" class="form-label">Jumlah</label>
                            <input type="number" wire:model="amount" id="tx_amount" name="amount" class="form-control">
                            @error('amount') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="tx_description" class="form-label">Deskripsi</label>
                            <textarea wire:model="description" id="tx_description" name="description" class="form-control"></textarea>
                        </div>

                        <div class="col-12">
                            <label for="tx_image" class="form-label">Bukti (opsional)</label>
                            <input type="file" wire:model="image" id="tx_image" name="image" class="form-control">
                            @if($existingImage)
                            <div class="mt-2">
                                <a target="_blank" href="{{ asset('storage/'.$existingImage) }}" class="link-primary small">Lihat bukti</a>
                                <button type="button" wire:click="removeImage" class="btn btn-link btn-sm text-danger">Hapus</button>
                            </div>
                            @endif
                            @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 text-end">
                            <button type="button" wire:click="closeForm" class="btn btn-outline-secondary me-2">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        let chart = null;

        function renderOrUpdateChart(payload) {
            const labels = payload.labels || [];
            const income = (payload.income || []).map(v => {
                const n = parseFloat(String(v).replace(/,/g, ''));
                return Number.isFinite(n) ? n : 0;
            });
            const expense = (payload.expense || []).map(v => {
                const n = parseFloat(String(v).replace(/,/g, ''));
                return Number.isFinite(n) ? n : 0;
            });

            const container = document.querySelector('#transactionsChart');

            if (!labels.length) {
                if (chart) {
                    try { chart.destroy(); } catch (e) {}
                    chart = null;
                }
                container.innerHTML = '<div class="p-4 text-center text-muted">Belum ada data untuk ditampilkan.</div>';
                return;
            }

            container.innerHTML = '';

            const options = {
                chart: { type: 'bar', height: 350 },
                series: [
                    { name: 'Pemasukan', data: income },
                    { name: 'Pengeluaran', data: expense }
                ],
                xaxis: { categories: labels },
                colors: ['#22c55e', '#ef4444'],
                dataLabels: { enabled: false },
                tooltip: { y: { formatter: function (val) { return new Intl.NumberFormat().format(val); } } }
            };

            if (!chart) {
                try {
                    chart = new ApexCharts(container, options);
                    chart.render();
                } catch (err) {
                    console.error('error creating/redering chart', err, { options, labels, income, expense });
                    try { if (chart && typeof chart.destroy === 'function') chart.destroy(); } catch (e) {}
                    chart = null;
                }
            } else {
                try {
                    chart.updateOptions({ xaxis: { categories: labels } });
                    chart.updateSeries([ { name: 'Pemasukan', data: income }, { name: 'Pengeluaran', data: expense } ]);
                } catch (e) {
                    try { chart.destroy(); } catch (ee) {}
                    try {
                        chart = new ApexCharts(container, options);
                        chart.render();
                    } catch (err) {
                        console.error('error recreating chart', err);
                        try { if (chart && typeof chart.destroy === 'function') chart.destroy(); } catch (e) {}
                        chart = null;
                    }
                }
            }
        }

        document.addEventListener('livewire:init', function () {
            function readPayloadFromDom() {
                const raw = document.getElementById('chart-data')?.textContent || '{}';
                try { return JSON.parse(raw); } catch (e) { console.error('invalid JSON in #chart-data', e); return {}; }
            }

            const initial = readPayloadFromDom();
            renderOrUpdateChart(initial);

            if (window.Livewire && typeof Livewire.hook === 'function') {
                Livewire.hook('message.processed', (message, component) => {
                    const payload = readPayloadFromDom();
                    renderOrUpdateChart(payload);
                });
            }

            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded.');
            }
        });
    })();
</script>
@endpush
