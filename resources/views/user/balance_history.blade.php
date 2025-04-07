@push('link')
<style>
    /* Base Styles */
    .history-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 16px;
    }
    
    .history-wrapper {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    /* Header Styles */
    .history-header {
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }
    
    .back-button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        margin-right: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    
    .header-content {
        display: flex;
        align-items: center;
        flex-grow: 1;
    }
    
    .history-header i {
        font-size: 20px;
    }
    
    .history-header h2 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }
    
    /* Filter Styles */
    .filter-section {
        padding: 16px;
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .filter-form {
        display: flex;
        gap: 10px;
    }
    
    .filter-select {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .filter-button {
        padding: 10px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }
    
    /* Summary Styles */
    .summary-cards {
        display: flex;
        gap: 10px;
        padding: 16px;
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .summary-card {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
    }
    
    .summary-income {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .summary-withdrawal {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .summary-net {
        background-color: #e3f2fd;
        color: #1565c0;
    }
    
    .summary-label {
        font-size: 12px;
        margin-bottom: 4px;
    }
    
    .summary-amount {
        font-size: 16px;
        font-weight: 600;
    }
    
    /* History Item Styles */
    .history-list {
        background-color: white;
    }
    
    .history-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s ease;
    }
    
    .history-item:last-child {
        border-bottom: none;
    }
    
    .history-amount {
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .income {
        color: #2e7d32;
    }
    
    .withdrawal {
        color: #c62828;
    }
    
    .history-detail {
        font-size: 14px;
        color: #555;
        margin-bottom: 4px;
    }
    
    .history-time {
        font-size: 12px;
        color: #888;
    }
    
    .history-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        margin-top: 4px;
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
    
    /* Pagination Styles */
    .history-pagination {
        display: flex;
        justify-content: center;
        padding: 20px;
        background-color: white;
    }
    
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .page-item {
        margin: 0 4px;
    }
    
    .page-link {
        display: block;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
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
        cursor: not-allowed;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 640px) {
        .history-container {
            padding: 8px;
        }
        
        .history-item {
            padding: 12px 16px;
        }
        
        .summary-cards {
            flex-direction: column;
        }
        
        .filter-form {
            flex-direction: column;
        }
        
        .history-header {
            padding: 16px 20px;
        }
        
        .history-header h2 {
            font-size: 18px;
        }
        
        .back-button {
            font-size: 18px;
        }
    }
</style>
@endpush

@extends('layouts.app')

@section('content')
<div class="history-container">
    <div class="history-wrapper">
        <!-- Header -->
        <div class="history-header">
            <button class="back-button" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="header-content">
                <i class="fas fa-history"></i>
                <h2>Riwayat Saldo</h2>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('balance.history') }}" class="filter-form">
                <select name="month" class="filter-select">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
                
                <select name="year" class="filter-select">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                
                <button type="submit" class="filter-button">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if($month || $year)
                    <a href="{{ route('balance.history') }}" class="filter-button" style="background-color: #f44336;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card summary-income">
                <div class="summary-label">Pemasukan</div>
                <div class="summary-amount">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            
            <div class="summary-card summary-withdrawal">
                <div class="summary-label">Penarikan Berhasil</div>
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
                <div class="history-amount income">+ Rp {{ number_format($item->amount, 0, ',', '.') }}</div>
                <div class="history-detail">Pemasukan dari {{ $item->note ?? 'Berlangganan' }}</div>
                @else
                    @if($item->status == 'pending')
                        <div class="history-amount" style="color: #ff8f00;">Rp {{ number_format($item->amount, 0, ',', '.') }}</div>
                    @else
                        <div class="history-amount withdrawal">- Rp {{ number_format($item->amount, 0, ',', '.') }}</div>
                    @endif
                    <div class="history-detail">
                        Penarikan ke 
                        @if(str_contains($item->method, 'bank_'))
                            Bank {{ strtoupper(str_replace('bank_', '', $item->method)) }}
                        @else
                            {{ ucfirst($item->method) }}
                        @endif
                        dengan nomor {{ preg_replace('/(?<=\d{3})\d(?=\d{2})/', 'X', $item->destination) }}
                        a/n {{ preg_replace('/(?<=.{1}).(?=.{1})/', 'X', $item->destination_name) }}
                    </div>
                @endif
                <div class="history-time">{{ $item->created_at->format('d M Y H:i') }}</div>
                @if($item->type == 'withdrawal')
                <span class="history-status status-{{ $item->status }}">
                    @if($item->status == 'pending') Menunggu
                    @elseif($item->status == 'success') Berhasil
                    @else Ditolak
                    @endif
                </span>
                @endif
            </div>
            @empty
            <div class="history-item" style="text-align: center; color: #888;">
                Tidak ada riwayat transaksi
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
                        <a class="page-link" href="{{ $riwayat->previousPageUrl() }}{{ $month ? '&month='.$month : '' }}{{ $year ? '&year='.$year : '' }}" rel="prev">&laquo;</a>
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
                            <a class="page-link" href="{{ $url }}{{ $month ? '&month='.$month : '' }}{{ $year ? '&year='.$year : '' }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($riwayat->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $riwayat->nextPageUrl() }}{{ $month ? '&month='.$month : '' }}{{ $year ? '&year='.$year : '' }}" rel="next">&raquo;</a>
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