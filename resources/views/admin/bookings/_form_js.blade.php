<script>
  /**
   * Fungsi untuk menghitung total harga berdasarkan paket dan addons.
   */
  function calculatePrice() {
      const packageSelect = document.getElementById('package_id');
      const grandTotalInput = document.getElementById('grand_total');
      const dpInput = document.getElementById('dp_amount');
      const addonCheckboxes = document.querySelectorAll('.addon-checkbox:checked');

      // Pastikan packageSelect memiliki opsi yang dipilih
      if (!packageSelect || packageSelect.selectedIndex === -1) {
        grandTotalInput.value = 0.00.toFixed(2);
        dpInput.value = 0.00.toFixed(2);
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

      // 3. Hitung Grand Total & DP (50%)
      const grandTotal = basePrice + addonsTotal;
      const dpAmount = grandTotal * 0.5;

      // 4. Update Input Fields (Gunakan toFixed(2) untuk konsistensi desimal)
      grandTotalInput.value = grandTotal.toFixed(2); 
      dpInput.value = dpAmount.toFixed(2);
  }

  /**
   * BARU: Fungsi untuk menampilkan/menyembunyikan input Sesi 2
   * berdasarkan ATRIBUT DURASI PAKET.
   */
   function handleSessionVisibility() {
      const packageSelect = document.getElementById('package_id');
      const session2Container = document.getElementById('session_2_container');
      const session2Input = document.getElementById('session_2_time');

      if (!packageSelect || packageSelect.selectedIndex === -1 || !session2Container) {
          return;
      }

      const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
      
      // --- LOGIKA BARU ---
      // Ambil durasi dari data-attribute
      const duration = parseFloat(selectedPackage.dataset.duration) || 0;
      // --- AKHIR LOGIKA BARU ---

      // Cek apakah durasi 2 jam atau lebih
      if (duration >= 2) {
          session2Container.style.display = 'block'; // Tampilkan
      } else {
          session2Container.style.display = 'none'; // Sembunyikan
          session2Input.value = ''; // Kosongkan nilainya saat disembunyikan
      }
  }

  // Attach event listeners
  document.addEventListener('DOMContentLoaded', function() {
      const packageSelect = document.getElementById('package_id');
      const addonsContainer = document.getElementById('addons-container');
      const addonCheckboxes = document.querySelectorAll('.addon-checkbox');

      // Panggil kalkulasi & visibilitas saat halaman dimuat (untuk old input atau data edit)
      calculatePrice(); 
      handleSessionVisibility(); // <-- Panggil fungsi baru

      // 1. Listener untuk perubahan Paket
      packageSelect.addEventListener('change', function() {
          calculatePrice();
          handleSessionVisibility(); // <-- Panggil fungsi baru
      }); 

      // 2. Listener untuk perubahan Checkbox Add-on
      if (addonsContainer) {
          addonsContainer.addEventListener('change', function(e) {
              if (e.target.classList.contains('addon-checkbox')) {
                  calculatePrice();
              }
          });
      }
  });
</script>