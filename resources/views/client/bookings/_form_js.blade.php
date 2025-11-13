<script>
  /**
   * Ini adalah "Gaya Admin" yang Anda minta.
   * Skrip hanya akan berjalan setelah semua HTML selesai dimuat.
   */
  document.addEventListener('DOMContentLoaded', function() {
      
      // --- Variabel ---
      // Kita ambil elemen-elemen ini HANYA SETELAH DOM siap
      const packageSelect = document.getElementById('package_id');
      const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
      const packagePriceDisplay = document.getElementById('package_price');
      const addonsTotalDisplay = document.getElementById('addons_total');
      const grandTotalDisplay = document.getElementById('grand_total_display');
      const grandTotalHidden = document.getElementById('grand_total_hidden');
      const packagePriceHidden = document.getElementById('package_price_hidden');
      const addonsTotalHidden = document.getElementById('addons_total_hidden');
      const session2Container = document.getElementById('session_2_client_container');
      const session2Input = document.getElementById('session_2_time');
  
      // --- Fungsi ---
  
      function formatRupiah(number) {
          return 'Rp ' + (Math.round(number)).toLocaleString('id-ID');
      }
  
      /**
       * Mengatur visibilitas Sesi 2
       */
      function handleSessionVisibility() {
          // Cek jika elemen ada (sebagai keamanan)
          if (!packageSelect || !session2Container) return; 
          
          // Cek jika ada opsi yang dipilih (bukan placeholder)
          if (packageSelect.selectedIndex <= 0) { // <= 0 untuk menangani placeholder
              session2Container.style.display = 'none';
              if (session2Input) session2Input.value = '';
              return;
          }
          
          const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
          const duration = parseFloat(selectedPackage.dataset.duration) || 0;
  
          if (duration >= 2) {
              session2Container.style.display = 'block';
          } else {
              session2Container.style.display = 'none';
              if (session2Input) session2Input.value = '';
          }
      }
  
      /**
       * Menghitung total harga
       */
      function calculateTotal() {
          if (!packageSelect) return;
  
          let packagePrice = 0;
          
          // Cek jika ada opsi yang dipilih (bukan placeholder)
          if (packageSelect.selectedIndex > 0) {
              const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
              packagePrice = parseFloat(selectedPackage.dataset.price) || 0;
          }
  
          let addonsTotal = 0;
          addonCheckboxes.forEach(cb => {
              if (cb.checked) {
                  addonsTotal += parseFloat(cb.dataset.price) || 0;
              }
          });
  
          const grandTotal = packagePrice + addonsTotal;
  
          // Update Display (tambahkan cek null untuk keamanan)
          if(packagePriceDisplay) packagePriceDisplay.textContent = formatRupiah(packagePrice);
          if(addonsTotalDisplay) addonsTotalDisplay.textContent = formatRupiah(addonsTotal);
          if(grandTotalDisplay) grandTotalDisplay.textContent = formatRupiah(grandTotal);
  
          // Update Hidden Inputs
          if(grandTotalHidden) grandTotalHidden.value = grandTotal.toFixed(2);
          if(packagePriceHidden) packagePriceHidden.value = packagePrice.toFixed(2);
          if(addonsTotalHidden) addonsTotalHidden.value = addonsTotal.toFixed(2);
      }
  
      // --- Event Listeners ---
      
      // Tambahkan listener HANYA jika elemennya ada
      // Ini adalah pengecekan krusial
      if (packageSelect) {
          packageSelect.addEventListener('change', function() {
              calculateTotal();
              handleSessionVisibility();
          });
      } else {
          // Jika ini muncul, berarti ID di HTML (Langkah 1) 100% SALAH.
          console.error("CRITICAL: document.getElementById('package_id') tidak ditemukan. Cek kembali HTML Anda.");
      }
  
      if (addonCheckboxes) {
          addonCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));
      }
  
      // --- Panggilan Awal ---
      // Panggil fungsi saat halaman dimuat (setelah DOM siap)
      // untuk mengatur nilai awal berdasarkan 'old' input atau paket default.
      calculateTotal();
      handleSessionVisibility();
  
  }); // Akhir dari DOMContentLoaded
  </script>