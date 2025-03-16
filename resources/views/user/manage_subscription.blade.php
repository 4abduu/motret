@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <h3 class="card-title mb-4">Atur Harga Langganan</h3>
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <form class="forms-sample" method="POST"  action="{{ route('subscription.save') }}">
                @csrf
                <div class="form-group">
                    <label for="price_1_month" class="form-label">Harga 1 Bulan (Wajib)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control" id="price_1_month" name="price_1_month" value="{{ $subscriptionPrices ? number_format($subscriptionPrices->price_1_month, 0, ',', '.') : '' }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price_3_months" class="form-label">Harga 3 Bulan (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control" id="price_3_months" name="price_3_months" value="{{ $subscriptionPrices ? number_format($subscriptionPrices->price_3_months, 0, ',', '.') : '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="price_6_months" class="form-label">Harga 6 Bulan (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control" id="price_6_months" name="price_6_months" value="{{ $subscriptionPrices ? number_format($subscriptionPrices->price_6_months, 0, ',', '.') : '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="price_1_year" class="form-label">Harga 1 Tahun (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control" id="price_1_year" name="price_1_year" value="{{ $subscriptionPrices ? number_format($subscriptionPrices->price_1_year, 0, ',', '.') : '' }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-success text-white">Simpan</button>
            </form>
          </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const formatNumber = (num) => {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };

    const inputs = document.querySelectorAll('input[type="text"]');
    inputs.forEach(input => {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\./g, '');
            if (!isNaN(value) && value !== '') {
                e.target.value = formatNumber(value);
            } else {
                e.target.value = '';
            }
        });
    });
});
</script>
@endpush