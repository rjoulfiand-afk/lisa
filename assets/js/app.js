/**
 * app.js — SiParkir (helper minimal)
 * SweetAlert2 sudah di-load via CDN di navbar.php
 * File ini hanya untuk helper kecil yang tidak perlu SweetAlert
 */

document.addEventListener('DOMContentLoaded', function () {

    // Auto-uppercase semua input nomor plat
    document.querySelectorAll('input[name="nomor_plat"], input[name="cari_plat"]').forEach(function (el) {
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            try { this.setSelectionRange(pos, pos); } catch(e) {}
        });
    });

    // Realtime clock di elemen #prk-clock (opsional)
    const clock = document.getElementById('prk-clock');
    if (clock) {
        function tick() {
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            clock.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        }
        tick();
        setInterval(tick, 1000);
    }

});
