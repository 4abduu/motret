@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-balance-history.css') }}">
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
                <div>
                    <h2>
                        <i class="fas fa-history"></i>
                        Riwayat Saldo
                    </h2>
                    <div class="user-info">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="user-avatar">
                        @else
                            <div class="user-avatar" style="background-color: #32bd40; color: white; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="user-name">{{ $user->name }}</div>
                    </div>
                </div>
            </div>
            {{-- <a href="{{ route('withdrawal.balance') }}" class="withdraw-btn top-end">
                <i class="fas fa-money-bill-wave"></i> Ajukan Penarikan
            </a> --}}
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.saldo.detail', $user->id) }}" class="filter-form">
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
                    <a href="{{ route('admin.saldo.detail', $user->id) }}" class="filter-button" style="background-color: #f44336;">
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
                        @if($item->destination_name)
                            a/n {{ $item->destination_name }}
                        @endif
                    </div>
                    
                    @if($item->status == 'rejected' && $item->rejection_reason)
                    <div class="history-detail" style="color: #c62828;">
                        Alasan penolakan: {{ $item->rejection_reason }}
                    </div>
                    @endif
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