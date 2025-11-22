<script>
    $(document).ready(function() {

        // Cek apakah kita dalam mode Edit berdasarkan keberadaan input hidden khusus
        // Jika elemen 'previous_total_paid' ada, berarti mode edit
        const isEditMode = $('#previous_total_paid').length > 0;

        function formatRupiah(number) {
            return 'Rp ' + (Math.round(number)).toLocaleString('id-ID');
        }

        function handleSessionVisibility() {
            const $packageSelect = $('#package_id');
            const $session2Container = $('#session_2_client_container');
            const $session2Input = $('#session_2_time');

            if (!$packageSelect.length || !$session2Container.length) return;

            const $selectedOption = $packageSelect.find('option:selected');
            if (!$selectedOption.val() || $selectedOption.val() == "") {
                $session2Container.hide();
                $session2Input.val('');
                return;
            }

            const duration = parseFloat($selectedOption.data('duration')) || 0;
            if (duration >= 2) {
                $session2Container.show();
            } else {
                $session2Container.hide();
                $session2Input.val('');
            }
        }

        function calculateTotal() {
            // 1. Harga Paket
            let packagePrice = 0;
            const selectedPackage = $('#package_id').find('option:selected');
            if (selectedPackage.length && selectedPackage.val() != "") {
                packagePrice = parseFloat(selectedPackage.data('price')) || 0;
            }

            // 2. Harga Addons
            let addonsTotal = 0;
            $('.addon-checkbox:checked').each(function() {
                addonsTotal += parseFloat($(this).data('price')) || 0;
            });

            // 3. Grand Total
            const grandTotal = packagePrice + addonsTotal;

            // Update Tampilan Umum
            $('#package_price').text(formatRupiah(packagePrice));
            $('#addons_total').text(formatRupiah(addonsTotal));
            $('#grand_total_display').text(formatRupiah(grandTotal));
            
            // Update Input Hidden Umum
            $('#package_price_hidden').val(packagePrice);
            $('#addons_total_hidden').val(addonsTotal);
            $('#grand_total').val(grandTotal);

            // 4. Logika Percabangan Mode
            if (isEditMode) {
                // --- LOGIKA UPGRADE ---
                const previousPaid = parseFloat($('#previous_total_paid').val()) || 0;
                
                // Target DP 50% dari harga baru
                const targetNewDP = grandTotal * 0.50;
                
                // Kurangi target dengan uang yang sudah masuk
                let additionalCost = targetNewDP - previousPaid;
                
                // Jika uang masuk sudah lebih besar, set 0
                if (additionalCost < 0) additionalCost = 0;

                $('#summary_additional_cost').text(formatRupiah(additionalCost));

            } else {
                // --- LOGIKA BOOKING BARU ---
                // Pastikan elemen radio ada sebelum mencoba mengambil nilainya
                if ($('input[name="payment_option"]').length > 0) {
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

                    $('#dp-amount-text').text(formatRupiah(dpAmount));
                    $('#final-amount-text').text(formatRupiah(finalAmount));
                }
            }
        }

        // Listeners
        $('#package_id').on('change', function() {
            calculateTotal();
            handleSessionVisibility();
        });
        $('.addon-checkbox').on('change', calculateTotal);
        $('input[name="payment_option"]').on('change', calculateTotal);

        // Init
        calculateTotal();
        handleSessionVisibility();
    });
</script>