{{-- Service worker registration + "Install app" banner (Android/desktop prompt, iOS instructions) --}}
<style>
    #pwa-install-banner {
        position: fixed; left: 12px; right: 12px; z-index: 1060;
        bottom: calc(16px + env(safe-area-inset-bottom));
        max-width: 440px; margin: 0 auto;
        display: none; align-items: center; gap: 12px;
        padding: 12px 14px; border-radius: 16px;
        background: #111827; color: #fff;
        box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.35);
        font-family: 'Inter', system-ui, sans-serif;
    }
    #pwa-install-banner.show { display: flex; }
    #pwa-install-banner img { width: 44px; height: 44px; border-radius: 11px; flex-shrink: 0; }
    #pwa-install-banner .pwa-text { flex: 1; min-width: 0; font-size: 0.82rem; line-height: 1.35; }
    #pwa-install-banner .pwa-text strong { display: block; font-size: 0.9rem; }
    #pwa-install-banner .pwa-install-btn {
        border: 0; border-radius: 10px; padding: 8px 14px;
        background: #4f46e5; color: #fff; font-weight: 600; font-size: 0.85rem; white-space: nowrap;
    }
    #pwa-install-banner .pwa-close {
        border: 0; background: none; color: rgba(255, 255, 255, 0.6);
        font-size: 1.2rem; line-height: 1; padding: 4px;
    }
    @media (max-width: 767.98px) {
        body:has(#bottom-nav) #pwa-install-banner { bottom: calc(84px + env(safe-area-inset-bottom)); }
    }
</style>

<div id="pwa-install-banner" role="dialog" aria-live="polite" aria-label="Pasang aplikasi">
    <img src="{{ asset('icons/icon-192.png') }}" alt="">
    <div class="pwa-text">
        <strong>Pasang Catatan Keuangan</strong>
        <span id="pwa-install-text">Buka lebih cepat langsung dari layar utama HP Anda.</span>
    </div>
    <button type="button" class="pwa-install-btn" id="pwa-install-btn">Pasang</button>
    <button type="button" class="pwa-close" id="pwa-install-close" aria-label="Tutup">&times;</button>
</div>

<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {});
        });
    }

    var DISMISS_KEY = 'pwa-install-dismissed-at';
    var banner = document.getElementById('pwa-install-banner');
    var installBtn = document.getElementById('pwa-install-btn');
    var text = document.getElementById('pwa-install-text');
    var sidebarItem = document.getElementById('sidebarInstallItem');
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var deferredPrompt = null;

    function recentlyDismissed() {
        try {
            return Date.now() - Number(localStorage.getItem(DISMISS_KEY) || 0) < 7 * 24 * 60 * 60 * 1000;
        } catch (e) {
            return false;
        }
    }

    function showIosHelp() {
        var msg = 'Ketuk tombol Bagikan (kotak dengan panah ke atas) di Safari, lalu pilih "Tambah ke Layar Utama".';
        if (window.Swal) {
            Swal.fire({ title: 'Pasang di iPhone', text: msg, icon: 'info', confirmButtonColor: '#4f46e5', confirmButtonText: 'Mengerti' });
        } else {
            alert(msg);
        }
    }

    function install() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.finally(function () {
                deferredPrompt = null;
                hide();
            });
        } else if (isIos) {
            showIosHelp();
        }
    }

    function show() {
        if (sidebarItem) sidebarItem.classList.remove('d-none');
        if (!recentlyDismissed()) banner.classList.add('show');
    }

    function hide() {
        banner.classList.remove('show');
    }

    if (isStandalone) return;

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        show();
    });

    window.addEventListener('appinstalled', function () {
        hide();
        if (sidebarItem) sidebarItem.classList.add('d-none');
    });

    if (isIos) {
        text.textContent = 'Tambahkan ke layar utama agar terasa seperti aplikasi.';
        installBtn.textContent = 'Caranya';
        show();
    }

    installBtn.addEventListener('click', install);
    if (sidebarItem) sidebarItem.querySelector('a').addEventListener('click', function (e) {
        e.preventDefault();
        install();
    });
    document.getElementById('pwa-install-close').addEventListener('click', function () {
        try { localStorage.setItem(DISMISS_KEY, String(Date.now())); } catch (e) {}
        hide();
    });
})();
</script>
