@extends('layouts.app')

@section('content')
<div class="saldo-container" style="max-width: 600px; margin: 30px auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <!-- Balance Display Card -->
    <div class="balance-card" style="background: linear-gradient(135deg, #32bd40, #2aa336); border-radius: 12px; padding: 20px; color: white; box-shadow: 0 4px 12px rgba(50, 189, 64, 0.2); margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="margin: 0 0 5px 0; font-size: 14px; opacity: 0.9;">Saldo Tersedia</p>
                <h2 style="margin: 0; font-size: 28px; font-weight: 600;">Rp {{ number_format($balance, 0, ',', '.') }}</h2>
            </div>
            <a href="{{ route('balance.history') }}" style="text-decoration: none; color: white; background-color: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 20px; font-size: 14px; display: flex; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
                    <path d="M8 0c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm0 14.5c3.59 0 6.5-2.91 6.5-6.5S11.59 1.5 8 1.5 1.5 4.41 1.5 8 4.41 14.5 8 14.5z"/>
                    <path d="M8 4.5c.28 0 .5.22.5.5v3h2.5c.28 0 .5.22.5.5s-.22.5-.5.5h-3c-.28 0-.5-.22-.5-.5v-3.5c0-.28.22-.5.5-.5z"/>
                </svg>
                Riwayat
            </a>
        </div>
    </div>

    <!-- Withdrawal Form -->
    <div class="withdrawal-form" style="background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #333; font-size: 20px;">Tarik Saldo</h3>
        
        <form action="{{ route('withdrawal.store') }}" method="POST" id="withdrawalForm">
            @csrf
            
            <!-- Method Selection -->
            <div style="margin-bottom: 20px;">
                <p style="margin-bottom: 10px; font-size: 14px; color: #555;">Metode Penarikan <span style="color: #e74c3c;">*</span></p>
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <label style="flex: 1; position: relative;">
                        <input type="radio" name="method_type" value="bank" style="position: absolute; opacity: 0;" required>
                        <div class="method-option" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#32bd40" viewBox="0 0 16 16" style="margin-bottom: 5px;">
                                <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2H3z"/>
                            </svg>
                            <p style="margin: 5px 0 0 0; font-size: 14px;">Transfer Bank</p>
                        </div>
                    </label>
                    
                    <label style="flex: 1; position: relative;">
                        <input type="radio" name="method_type" value="ewallet" style="position: absolute; opacity: 0;" required>
                        <div class="method-option" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#32bd40" viewBox="0 0 16 16" style="margin-bottom: 5px;">
                                <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm1 0v8h14V4H1zm5 1.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1zm3 0a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1z"/>
                            </svg>
                            <p style="margin: 5px 0 0 0; font-size: 14px;">E-Wallet</p>
                        </div>
                    </label>
                </div>
                
                <!-- Bank Selection (Hidden by default) -->
                <div id="bank-list" class="method-details" style="display: none;">
                    <select name="method" id="bank-method" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; font-size: 14px;" disabled required>
                        <option value="">-- Pilih Bank --</option>
                        <option value="bank_bca">Bank BCA</option>
                        <option value="bank_bri">Bank BRI</option>
                        <option value="bank_bni">Bank BNI</option>
                        <option value="bank_mandiri">Bank Mandiri</option>
                        <option value="bank_cimb">Bank CIMB Niaga</option>
                    </select>
                </div>
                
                <!-- E-Wallet Selection (Hidden by default) -->
                <div id="ewallet-list" class="method-details" style="display: none;">
                    <select name="method" id="ewallet-method" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; font-size: 14px;" disabled required>
                        <option value="">-- Pilih E-Wallet --</option>
                        <option value="dana">Dana</option>
                        <option value="ovo">OVO</option>
                        <option value="gopay">Gopay</option>
                        <option value="shopeepay">ShopeePay</option>
                        <option value="linkaja">LinkAja</option>
                    </select>
                </div>
            </div>
            
            <!-- Account Number -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #555;">Nomor Rekening / E-Wallet <span style="color: #e74c3c;">*</span></label>
                <input type="text" name="destination" placeholder="Contoh: 1234567890" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
            </div>
            
            <!-- Account Name -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #555;">Atas Nama <span style="color: #e74c3c;">*</span></label>
                <input type="text" name="destination_name" placeholder="Nama pemilik rekening/ewallet" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
            </div>
            
            <!-- Amount -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #555;">Nominal Penarikan <span style="color: #e74c3c;">*</span></label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #555;">Rp</span>
                    <input type="number" name="amount" placeholder="Masukkan nominal" style="width: 100%; padding: 12px 12px 12px 35px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                </div>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #888;">Minimal penarikan Rp 10.000</p>
            </div>
            
            <!-- Note -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #555;">Catatan (Opsional)</label>
                <textarea name="note" placeholder="Contoh: Penarikan untuk kebutuhan mendesak" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; min-height: 80px; resize: vertical;"></textarea>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" style="width: 100%; padding: 14px; background-color: #32bd40; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                Ajukan Penarikan
            </button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('withdrawalForm');
    const methodRadios = document.querySelectorAll('input[name="method_type"]');
    const bankSelect = document.getElementById('bank-method');
    const ewalletSelect = document.getElementById('ewallet-method');
    
    methodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all method details first
            document.querySelectorAll('.method-details').forEach(detail => {
                detail.style.display = 'none';
            });
            
            // Disable all selects
            bankSelect.disabled = true;
            ewalletSelect.disabled = true;
            bankSelect.removeAttribute('required');
            ewalletSelect.removeAttribute('required');
            
            // Reset all method option styles
            document.querySelectorAll('.method-option').forEach(option => {
                option.style.borderColor = '#ddd';
                option.style.backgroundColor = 'transparent';
            });
            
            // Show selected method details
            if (this.value === 'bank') {
                document.getElementById('bank-list').style.display = 'block';
                bankSelect.disabled = false;
                bankSelect.setAttribute('required', 'required');
                this.parentElement.querySelector('.method-option').style.borderColor = '#32bd40';
                this.parentElement.querySelector('.method-option').style.backgroundColor = 'rgba(50, 189, 64, 0.05)';
            } else if (this.value === 'ewallet') {
                document.getElementById('ewallet-list').style.display = 'block';
                ewalletSelect.disabled = false;
                ewalletSelect.setAttribute('required', 'required');
                this.parentElement.querySelector('.method-option').style.borderColor = '#32bd40';
                this.parentElement.querySelector('.method-option').style.backgroundColor = 'rgba(50, 189, 64, 0.05)';
            }
        });
    });
    
    // Add click handler for method options to trigger radio change
    document.querySelectorAll('.method-option').forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.parentElement.querySelector('input[type="radio"]');
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        });
    });
    
    // Custom form validation to prevent hidden select validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Check if a method type is selected
        const methodSelected = document.querySelector('input[name="method_type"]:checked');
        if (!methodSelected) {
            isValid = false;
            alert('Silakan pilih metode penarikan (Bank atau E-Wallet)');
        }
        
        // Check if a specific method is selected
        if (methodSelected) {
            const methodSelect = methodSelected.value === 'bank' ? bankSelect : ewalletSelect;
            if (methodSelect.value === '') {
                isValid = false;
                alert('Silakan pilih ' + (methodSelected.value === 'bank' ? 'bank' : 'e-wallet') + ' tujuan');
            }
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush