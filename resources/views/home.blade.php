@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
    <div class="text-center">
        <h1>Halo, Selamat Datang di Motret</h1>
        <div class="mt-4">
            @if(Auth::check())
                <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('login') }}" onclick="openRegisterModal(event)" class="btn btn-secondary">Register</a>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRegisterModal(event) {
        event.preventDefault();
        window.location.href = "{{ route('login') }}?register=true";
    }
</script>
@endpush