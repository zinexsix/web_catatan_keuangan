{{-- Navigation progress bar + early page fetch on tap (shared by all layouts) --}}
<div id="nav-progress" aria-hidden="true"></div>

{{-- Start fetching a page as soon as a link is pressed (Chrome/Edge). Pages with side effects or downloads are excluded. --}}
<script type="speculationrules">
{
    "prefetch": [{
        "where": {
            "and": [
                { "href_matches": "/*" },
                { "not": { "href_matches": [
                    "/logout", "/auth/*", "/notifications/*", "/receipts/*", "/verify-email/*",
                    "/reports/pdf", "/reports/excel", "/reports/print", "/settings/backup"
                ] } },
                { "not": { "selector_matches": "[target=_blank], [download], [data-no-prefetch]" } }
            ]
        },
        "eagerness": "conservative"
    }]
}
</script>

<script>
(function () {
    var bar = document.getElementById('nav-progress');
    var NO_PROGRESS = /\/(reports\/(pdf|excel)|settings\/backup)(\?|$)/;
    var timer = null;

    function start() {
        clearTimeout(timer);
        bar.classList.remove('done');
        void bar.offsetWidth; // restart the transition
        bar.classList.add('loading');
        timer = setTimeout(finish, 12000); // never leave the bar hanging (e.g. slow or cancelled loads)
    }

    function finish() {
        clearTimeout(timer);
        if (!bar.classList.contains('loading')) return;
        bar.classList.remove('loading');
        bar.classList.add('done');
    }

    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        var link = e.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download') || link.dataset.bsToggle) return;
        var url = new URL(link.href, location.href);
        if (url.origin !== location.origin || NO_PROGRESS.test(url.pathname + url.search)) return;
        if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
        start();
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (e.defaultPrevented || form.target === '_blank') return;
        if (NO_PROGRESS.test(new URL(form.action || location.href, location.href).pathname)) return;
        start();
    });

    // Back/forward cache restores and normal loads both end the bar
    window.addEventListener('pageshow', finish);
    window.addEventListener('pagehide', function () { clearTimeout(timer); });
})();
</script>
