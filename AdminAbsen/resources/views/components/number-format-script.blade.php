<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to format number with dots
    function formatRupiah(num) {
        if (!num || num == 0) return '0';
        return parseInt(num).toLocaleString('id-ID');
    }

    // Monitor changes to tunjangan fields
    function setupTunjanganFormatting() {
        const jabatanField = document.querySelector('input[name="jabatan_tunjangan"]');
        const posisiField = document.querySelector('input[name="posisi_tunjangan"]');

        if (jabatanField) {
            // Format existing value
            if (jabatanField.value && jabatanField.value !== '0') {
                jabatanField.value = formatRupiah(jabatanField.value);
            }

            // Monitor for changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        const value = jabatanField.value;
                        if (value && value !== '0' && !value.includes('.')) {
                            jabatanField.value = formatRupiah(value);
                        }
                    }
                });
            });
            observer.observe(jabatanField, { attributes: true, attributeOldValue: true });
        }

        if (posisiField) {
            // Format existing value
            if (posisiField.value && posisiField.value !== '0') {
                posisiField.value = formatRupiah(posisiField.value);
            }

            // Monitor for changes
            const observer2 = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        const value = posisiField.value;
                        if (value && value !== '0' && !value.includes('.')) {
                            posisiField.value = formatRupiah(value);
                        }
                    }
                });
            });
            observer2.observe(posisiField, { attributes: true, attributeOldValue: true });
        }
    }

    // Setup initial formatting
    setupTunjanganFormatting();

    // Re-setup after Livewire updates
    document.addEventListener('livewire:updated', function () {
        setTimeout(setupTunjanganFormatting, 100);
    });
});
</script>
