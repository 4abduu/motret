@push('link')
<style>
    /* Base Styles */
    .history-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    /* Header Styles */
    .history-header {
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        padding: 18px 24px;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .back-button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        margin-right: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    
    .header-content {
        flex-grow: 1;
    }
    
    .history-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
    }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
    }
    
    .user-name {
        font-size: 1.1rem;
        font-weight: 500;
    }
    
    /* Filter Styles */
    .filter-section {
        background-color: white;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .filter-form {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .filter-select {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        height: 42px;
    }
    
    .filter-button {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .filter-button:hover {
        background-color: #3e8e41;
    }
    
    .reset-button {
        background-color: #f44336;
    }
    
    .reset-button:hover {
        background-color: #d32f2f;
    }
    
    /* Summary Cards */
    .summary-cards {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .summary-card {
        flex: 1;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .summary-income {
        background-color: #e8f5e9;
        color: #2e7d32;
        border-left: 4px solid #2e7d32;
    }
    
    .summary-withdrawal {
        background-color: #ffebee;
        color: #c62828;
        border-left: 4px solid #c62828;
    }
    
    .summary-net {
        background-color: #e3f2fd;
        color: #1565c0;
        border-left: 4px solid #1565c0;
    }
    
    .summary-label {
        font-size: 14px;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .summary-amount {
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    /* History List */
    .history-list {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .history-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s ease;
    }
    
    .history-item:last-child {
        border-bottom: none;
    }
    
    .history-item:hover {
        background-color: rgba(76, 175, 80, 0.05);
    }
    
    .history-amount {
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 1.1rem;
    }
    
    .income {
        color: #2e7d32;
    }
    
    .withdrawal {
        color: #c62828;
    }
    
    .pending {
        color: #ff8f00;
    }
    
    .history-detail {
        font-size: 14px;
        color: #555;
        margin-bottom: 6px;
        line-height: 1.4;
    }
    
    .history-time {
        font-size: 13px;
        color: #888;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .history-time i {
        font-size: 12px;
    }
    
    .history-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        margin-top: 6px;
    }
    
    .status-pending {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .status-success {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .status-rejected {
        background-color: #ffebee;
        color: #c62828;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }
    
    .empty-state i {
        font-size: 40px;
        margin-bottom: 15px;
        color: #ddd;
    }
    
    /* Pagination */
    .history-pagination {
        display: flex;
        justify-content: center;
        padding: 20px;
        background-color: white;
        border-radius: 0 0 8px 8px;
    }
    
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 8px;
    }
    
    .page-link {
        display: block;
        padding: 8px 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        color: #4CAF50;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .page-link:hover {
        background-color: #f5f5f5;
    }
    
    .page-item.active .page-link {
        background-color: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }
    
    .page-item.disabled .page-link {
        color: #aaa;
        pointer-events: none;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .history-header {
            padding: 15px;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .user-info {
            margin-top: 8px;
        }
        
        .filter-form {
            flex-direction: column;
            gap: 10px;
        }
        
        .filter-select, .filter-button {
            width: 100%;
        }
        
        .summary-cards {
            flex-direction: column;
            gap: 10px;
        }
        
        .history-item {
            padding: 12px 15px;
        }
        
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>
@endpush

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="history-container">
        <!-- Header -->
        <div class="history-header">
            <button class="back-button" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="header-content">
                <h2>
                    <i class="fas fa-history me-2"></i>Riwayat Saldo
                </h2>
                <div class="user-info">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                             alt="{{ $user->name }}" 
                             class="user-avatar">
                    @else
                        <div class="user-avatar bg-success d-flex align-items-center justify-content-center text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="user-name">{{ $user->name }}</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.saldo.detail', $user->id) }}" class="filter-form">
                <select name="month" class="filter-select">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
                
                <select name="year" class="filter-select">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                
                <button type="submit" class="filter-button">
                    <i class="fas fa-filter"></i> Filter
                </button>
                
                @if(request('month') || request('year'))
                    <a href="{{ route('admin.saldo.detail', $user->id) }}" class="filter-button reset-button">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card summary-income">
                <div class="summary-label">Total Pemasukan</div>
                <div class="summary-amount">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            
            <div class="summary-card summary-withdrawal">
                <div class="summary-label">Total Penarikan</div>
                <div class="summary-amount">- Rp {{ number_format($totalWithdrawal, 0, ',', '.') }}</div>
            </div>
            
            <div class="summary-card summary-net">
                <div class="summary-label">Saldo Saat Ini</div>
                <div class="summary-amount">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- History List -->
        <div class="history-list">
            @forelse($riwayat as $item)
            <div class="history-item">
                @if($item->type == 'income')
                <div class="history-amount income">
                    <i class="fas fa-plus-circle me-2"></i>+ Rp {{ number_format($item->amount, 0, ',', '.') }}
                </div>
                <div class="history-detail">
                    <i class="fas fa-info-circle me-2 text-muted"></i>
                    Pemasukan dari {{ $item->note ?? 'Berlangganan' }}
                </div>
                @else
                    @if($item->status == 'pending')
                        <div class="history-amount pending">
                            <i class="fas fa-clock me-2"></i>Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </div>
                    @elseif($item->status == 'success')
                        <div class="history-amount withdrawal">
                            <i class="fas fa-minus-circle me-2"></i>- Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="history-amount withdrawal">
                            <i class="fas fa-times-circle me-2"></i>- Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </div>
                    @endif
                    
                    <div class="history-detail">
                        <i class="fas fa-exchange-alt me-2 text-muted"></i>
                        Penarikan ke 
                        @if(str_contains($item->method, 'bank_'))
                            Bank {{ strtoupper(str_replace('bank_', '', $item->method)) }}
                        @else
                            {{ ucfirst($item->method) }}
                        @endif
                        ({{ $item->destination }})
                    </div>
                @endif
                
                <div class="history-time">
                    <i class="far fa-clock"></i>
                    {{ $item->created_at->format('d M Y H:i') }}
                </div>
                
                @if($item->type == 'withdrawal')
                <span class="history-status status-{{ $item->status }}">
                    @if($item->status == 'pending') Menunggu Konfirmasi
                    @elseif($item->status == 'success') Berhasil
                    @else Ditolak
                    @endif
                </span>
                @endif
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>Tidak ada riwayat transaksi</h4>
                <p>Tidak ditemukan data transaksi untuk periode ini</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($riwayat->hasPages())
        <div class="history-pagination">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if($riwayat->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $riwayat->previousPageUrl() }}{{ request('month') ? '&month='.request('month') : '' }}{{ request('year') ? '&year='.request('year') : '' }}" rel="prev">&laquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
                    @if($page == $riwayat->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}{{ request('month') ? '&month='.request('month') : '' }}{{ request('year') ? '&year='.request('year') : '' }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($riwayat->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $riwayat->nextPageUrl() }}{{ request('month') ? '&month='.request('month') : '' }}{{ request('year') ? '&year='.request('year') : '' }}" rel="next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">&raquo;</span>
                    </li>
                @endif
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection