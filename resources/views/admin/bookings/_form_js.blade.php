<script>
    /**
     * Fungsi untuk menghitung total harga berdasarkan paket dan addons.
     */
    function calculatePrice() {
        const packageSelect = document.getElementById('package_id');
        const grandTotalInput = document.getElementById('grand_total');
        const dpInput = document.getElementById('dp_amount');
        const dpLabel = document.getElementById('dp_amount_label'); // ⬅️ BARU: Ambil label
        const addonCheckboxes = document.querySelectorAll('.addon-checkbox:checked');
  
        // Ambil status payment option
        const paymentOptionFull = document.querySelector('.payment-option-radio[value="full"]');
        const isFullPayment = paymentOptionFull && paymentOptionFull.checked; // ⬅️ BARU: Cek status
  
        // Pastikan packageSelect memiliki opsi yang dipilih
        if (!packageSelect || packageSelect.selectedIndex === -1) {
          grandTotalInput.value = (0.00).toFixed(2);
          dpInput.value = (0.00).toFixed(2);
          return;
        }
  
        // 1. Ambil Harga Dasar (Base Price)
        const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
        let basePrice = parseFloat(selectedPackage.dataset.price) || 0; 
  
        // 2. Hitung Total Add-ons
        let addonsTotal = 0;
        addonCheckboxes.forEach(checkbox => {
            addonsTotal += parseFloat(checkbox.dataset.price) || 0;
        });
  
        // 3. Hitung Grand Total
        const grandTotal = basePrice + addonsTotal;
        let dpAmount = 0;
  
        // 4. LOGIKA BARU: Tentukan jumlah DP berdasarkan payment_option
        if (isFullPayment) {
            dpAmount = grandTotal;
            if (dpLabel) dpLabel.textContent = 'Jumlah Bayar (Lunas 100%)'; // ⬅️ BARU: Ubah label
        } else {
            dpAmount = grandTotal * 0.5;
            if (dpLabel) dpLabel.textContent = 'Jumlah DP Diharapkan (50%)'; // ⬅️ BARU: Ubah label
        }
  
        // 5. Update Input Fields
        grandTotalInput.value = grandTotal.toFixed(2); 
        dpInput.value = dpAmount.toFixed(2); // ⬅️ BARU: Nilai DP dinamis
    }
  
    /**
     * Fungsi untuk menampilkan/menyembunyikan input Sesi 2
     */
     function handleSessionVisibility() {
        // ... (Kode fungsi handleSessionVisibility Anda sudah benar, biarkan saja) ...
        const packageSelect = document.getElementById('package_id');
        const session2Container = document.getElementById('session_2_container');
        const session2Input = document.getElementById('session_2_time');
  
        if (!packageSelect || packageSelect.selectedIndex === -1 || !session2Container) {
            return;
        }
  
        const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
        const duration = parseFloat(selectedPackage.dataset.duration) || 0;
  
        if (duration >= 2) {
            session2Container.style.display = 'block';
        } else {
            session2Container.style.display = 'none';
            session2Input.value = '';
        }
     }
  
    // Attach event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const packageSelect = document.getElementById('package_id');
        const addonsContainer = document.getElementById('addons-container');
        const paymentRadios = document.querySelectorAll('.payment-option-radio'); // ⬅️ BARU
  
        // Panggil kalkulasi & visibilitas saat halaman dimuat
        calculatePrice(); 
        handleSessionVisibility();
  
        // 1. Listener untuk perubahan Paket
        packageSelect.addEventListener('change', function() {
            calculatePrice();
            handleSessionVisibility();
        }); 
  
        // 2. Listener untuk perubahan Checkbox Add-on
        if (addonsContainer) {
            addonsContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('addon-checkbox')) {
                    calculatePrice();
                }
            });
        }
  
        // 3. ⬅️ BARU: Listener untuk radio button payment option
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', calculatePrice);
        });
    });
  </script>