<script>
    // Ini adalah cara jQuery untuk 'DOMContentLoaded'
    // Skrip ini hanya akan berjalan jika jQuery sudah dimuat.
    $(document).ready(function() {

        // --- Fungsi Helper ---

        function formatRupiah(number) {
            return 'Rp ' + (Math.round(number)).toLocaleString('id-ID');
        }

        /**
         * Mengatur visibilitas Sesi 2 (versi jQuery)
         */
        function handleSessionVisibility() {
            const $packageSelect = $('#package_id');
            const $session2Container = $('#session_2_client_container');
            const $session2Input = $('#session_2_time');

            // Cek jika elemen ada
            if (!$packageSelect.length || !$session2Container.length) return;

            // Dapatkan 'option' yang dipilih
            const $selectedOption = $packageSelect.find('option:selected');
            
            // Cek jika itu placeholder (value-nya kosong)
            if (!$selectedOption.val() || $selectedOption.val() == "") {
                $session2Container.hide(); // Sembunyikan
                $session2Input.val('');
                return;
            }

            // Ambil data-duration dari opsi yang dipilih
            const duration = parseFloat($selectedOption.data('duration')) || 0;

            if (duration >= 2) {
                $session2Container.show(); // Tampilkan
            } else {
                $session2Container.hide(); // Sembunyikan
                $session2Input.val('');
            }
        }

        /**
         * Menghitung total harga (Fungsi Anda, sudah benar)
         */
        function calculateTotal() {
            // 1. Ambil harga paket
            let packagePrice = 0;
            const selectedPackage = $('#package_id').find('option:selected');
            if (selectedPackage.length && selectedPackage.val() != "") {
                packagePrice = parseFloat(selectedPackage.data('price')) || 0;
            }

            // 2. Ambil harga addons
            let addonsTotal = 0;
            // Gunakan class '.addon-checkbox' yang Anda definisikan di HTML
            $('.addon-checkbox:checked').each(function() {
                addonsTotal += parseFloat($(this).data('price')) || 0;
            });

            // 3. Hitung grandTotal
            const grandTotal = packagePrice + addonsTotal;

            // 4. Logika Opsi Pembayaran
            const paymentOption = $('input[name="payment_option"]:checked').val();
            let dpAmount = 0;
            let finalAmount = 0;

            if (paymentOption === 'full') {
                dpAmount = grandTotal;
                finalAmount = 0;
                $('#dp-label-text').text('Bayar Lunas (100%):');
                $('#final-label-text').text('Sisa Tagihan:');
            } else {
                dpAmount = grandTotal * 0.5;
                finalAmount = grandTotal * 0.5;
                $('#dp-label-text').text('Bayar DP (50%):');
                $('#final-label-text').text('Sisa Tagihan (50%):');
            }

            // 5. Tampilkan semua nilai ke ID yang benar
            $('#package_price').text(formatRupiah(packagePrice));
            $('#addons_total').text(formatRupiah(addonsTotal));
            $('#grand_total_display').text(formatRupiah(grandTotal));
            $('#dp-amount-text').text(formatRupiah(dpAmount));
            $('#final-amount-text').text(formatRupiah(finalAmount));
            
            // 6. Set input hidden
            $('#package_price_hidden').val(packagePrice);
            $('#addons_total_hidden').val(addonsTotal);
            $('#grand_total').val(grandTotal);
        }

        // --- Event Listeners (Versi jQuery) ---
        
        // 1. Listener untuk Paket
        $('#package_id').on('change', function() {
            calculateTotal();
            handleSessionVisibility();
        });

        // 2. Listener untuk Addons (gunakan class)
        $('.addon-checkbox').on('change', calculateTotal);

        // 3. Listener untuk Opsi Pembayaran
        $('input[name="payment_option"]').on('change', calculateTotal);

        // --- Panggilan Awal ---
        // Panggil fungsi saat halaman dimuat untuk mengatur nilai awal
        calculateTotal();
        handleSessionVisibility();

    }); // Akhir dari $(document).ready
</script>