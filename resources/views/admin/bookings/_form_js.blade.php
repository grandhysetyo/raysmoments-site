<script>
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

  // Attach event listeners
  document.addEventListener('DOMContentLoaded', function() {
      const packageSelect = document.getElementById('package_id');
      const addonsContainer = document.getElementById('addons-container');
      const addonCheckboxes = document.querySelectorAll('.addon-checkbox');

      // Panggil kalkulasi saat halaman dimuat (untuk old input atau data edit)
      calculatePrice(); 

      // 1. Listener untuk perubahan Paket
      packageSelect.addEventListener('change', calculatePrice); 

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