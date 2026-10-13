/*
 * Service worker for the installable app (PWA).
 *
 * Pages contain private financial data, so they are never cached: every page
 * is loaded from the network, and only an offline fallback page plus the app
 * icons are stored locally.
 */
const CACHE = 'catatan-keuangan-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/icons/icon-192.png', '/icons/favicon-32.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
    }
});
