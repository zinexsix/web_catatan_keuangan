{{-- Smooth page transitions, navigation progress bar and touch feedback (shared by all layouts) --}}
<style>
    /* Cross-page transitions (Chrome/Edge 126+, Safari 18.2+). Other browsers simply navigate normally. */
    @view-transition { navigation: auto; }

    /* Bars stay put while the page content (root) cross-fades underneath them.
       Only fixed/sticky elements get names, so modals inside the content are unaffected. */
    #top-navbar  { view-transition-name: top-navbar; }
    #bottom-nav  { view-transition-name: bottom-nav; }
    #sidebar     { view-transition-name: sidebar; }

    ::view-transition-group(top-navbar),
    ::view-transition-group(bottom-nav) { animation-duration: 0s; }
    ::view-transition-group(sidebar) { animation-duration: 0.3s; animation-timing-function: cubic-bezier(0.22, 1, 0.36, 1); }

    ::view-transition-old(root) {
        animation: vt-fade-out 0.16s ease-in both;
    }
    ::view-transition-new(root) {
        animation: vt-fade-in 0.32s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes vt-fade-out { to { opacity: 0; transform: translateY(-4px); } }
    @keyframes vt-fade-in { from { opacity: 0; transform: translateY(10px); } }

    /* Fallback entrance for browsers without cross-page view transitions */
    @supports not (view-transition-name: none) {
        #page-content, .auth-container { animation: vt-fade-in 0.35s cubic-bezier(0.22, 1, 0.36, 1) both; }
    }

    /* Thin progress bar shown while the next page loads */
    #nav-progress {
        position: fixed; top: 0; left: 0; z-index: 2000;
        height: 3px; width: 100%;
        background: linear-gradient(90deg, #818cf8, #4f46e5);
        box-shadow: 0 0 8px rgba(79, 70, 229, 0.5);
        transform: scaleX(0); transform-origin: left;
        opacity: 0; pointer-events: none;
        view-transition-name: nav-progress;
    }
    #nav-progress.loading {
        opacity: 1;
        transform: scaleX(0.85);
        transition: transform 2.5s cubic-bezier(0.1, 0.7, 0.2, 1), opacity 0.15s ease;
    }
    #nav-progress.done {
        opacity: 0;
        transform: scaleX(1);
        transition: transform 0.2s ease, opacity 0.3s ease 0.15s;
    }

    /* Snappier taps on touch screens: no grey flash, no double-tap zoom delay */
    a, button, .btn, .nav-link, .bn-item, [role="button"] {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }
    .btn:active:not(:disabled), .bn-item:active, .navbar-icon-btn:active, .navbar-toggle-btn:active {
        transform: scale(0.96);
    }
    .bn-item, .bn-add, .navbar-icon-btn, .navbar-toggle-btn {
        transition: transform 0.15s ease, color 0.2s ease, background-color 0.2s ease;
    }
    .bn-item.active i { animation: bn-pop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
    @keyframes bn-pop { 0% { transform: scale(0.8); } 100% { transform: scale(1); } }

    /* Dropdowns and modals ease in instead of popping */
    .dropdown-menu.show { animation: dropdown-in 0.18s cubic-bezier(0.22, 1, 0.36, 1); transform-origin: top right; }
    @keyframes dropdown-in { from { opacity: 0; transform: translateY(-6px) scale(0.98); } }
    .modal.fade .modal-dialog { transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1); transform: translateY(16px) scale(0.98); }
    .modal.show .modal-dialog { transform: none; }

    @media (prefers-reduced-motion: reduce) {
        @view-transition { navigation: none; }
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
