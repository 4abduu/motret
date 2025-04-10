@push('link')
<style>
    /* Base Styles */
    .notification-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 16px;
    }
    
    .notification-wrapper {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    /* Header Styles */
    .notification-header {
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
    
    .notification-header i {
        font-size: 20px;
    }
    
    .notification-header h2 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }
    
    /* Notification Item Styles */
    .notification-list {
        background-color: white;
    }
    
    .notification-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }
    
    .notification-item:hover {
        background-color: #f9f9f9;
    }
    
    .notification-item.unread {
        background-color: #f5faf5;
    }
    
    .notification-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .notification-icon.follow {
        background-color: #2196F3;
    }
    
    .notification-icon.like {
        background-color: #F44336;
    }
    
    .notification-icon.comment {
        background-color: #FF9800;
    }
    
    .notification-icon.reply {
        background-color: #673AB7;
    }
    
    .notification-icon.system {
        background-color: #607D8B;
    }
    
    .notification-content {
        flex-grow: 1;
        min-width: 0;
    }
    
    .notification-sender {
        font-weight: 600;
        color: #333;
        text-decoration: none;
        margin-right: 4px;
    }
    
    .notification-sender:hover {
        text-decoration: underline;
    }
    
    .notification-message {
        color: #555;
        word-break: break-word;
    }
    
    .notification-time {
        font-size: 12px;
        color: #888;
        margin-top: 4px;
    }
    
    .sender-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px;
    }
    
    /* Pagination Styles */
    .notification-pagination {
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
        .notification-container {
            padding: 8px;
        }
        
        .notification-item {
            padding: 12px 16px;
            gap: 12px;
        }
        
        .notification-icon, .sender-avatar {
            width: 36px;
            height: 36px;
        }
        
        .notification-header {
            padding: 16px 20px;
        }
        
        .notification-header h2 {
            font-size: 18px;
        }
        
        .back-button {
            font-size: 18px;
        }
        
        .page-link {
            padding: 6px 10px;
            font-size: 14px;
        }
    }
</style>
@endpush

@extends('layouts.app')

@section('content')
<div class="notification-container">
    <div class="notification-wrapper">
        <!-- Header Notifikasi -->
        <div class="notification-header">
            <button class="back-button" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="header-content">
                <i class="fas fa-bell me-2"></i>
                <h2>Notifikasi</h2>
            </div>
        </div>

        <!-- Container Notifikasi -->
        <div class="notification-list">
            @foreach($notifications as $notification)
            <div class="notification-item {{ !$notification->read_at ? 'unread' : '' }}">
                <!-- Icon -->
                <div class="notification-icon {{ $notification->type }}">
                    @switch($notification->type)
                        @case('follow')
                            <i class="fas fa-user-plus"></i>
                            @break
                        @case('like')
                            <i class="fas fa-heart"></i>
                            @break
                        @case('comment')
                            <i class="fas fa-comment"></i>
                            @break
                        @case('reply')
                            <i class="fas fa-reply"></i>
                            @break
                        @case('system')
                            <i class="fas fa-info-circle"></i>
                            @break
                        @default
                            <i class="fas fa-bell"></i>
                    @endswitch
                </div>

                <!-- Konten Notifikasi -->
                <div class="notification-content">
                    <div style="display: flex; align-items: center;">
                        @if($notification->type !== 'system' && $notification->sender)
                            <a href="{{ route('user.showProfile', $notification->sender->username) }}">
                                <img src="{{ asset($notification->sender->profile_photo ? 'storage/photo_profile/' . $notification->sender->profile_photo : 'images/foto profil.jpg') }}" 
                                     alt="{{ $notification->sender->username }}" 
                                     class="sender-avatar">
                            </a>
                        @endif
                        
                        <div>
                            @if($notification->sender)
                                <a href="{{ route('user.showProfile', $notification->sender->username) }}" 
                                   class="notification-sender">
                                    {{ $notification->sender->username }}
                                </a>
                            @endif
                            
                            <span class="notification-message">
                                @switch($notification->type)
                                    @case('follow')
                                        mengikuti Anda
                                        @break
                                    @case('like')
                                        menyukai foto Anda
                                        @break
                                    @case('comment')
                                        mengomentari foto Anda
                                        @break
                                    @case('reply')
                                        membalas komentar Anda
                                        @break
                                    @case('system')
                                        {{ $notification->message }}
                                        @break
                                    @default
                                        {{ $notification->message }}
                                @endswitch
                            </span>
                            
                            <div class="notification-time">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="notification-pagination">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if($notifications->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $notifications->previousPageUrl() }}" rel="prev">&laquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                    @if($page == $notifications->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($notifications->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $notifications->nextPageUrl() }}" rel="next">&raquo;</a>
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