/*
 * Service worker for the installable app (PWA).
 *
 * Pages contain private financial data, so they are never cached: every page
 * is loaded from the network, and only an offline fallback page plus the app
 * icons are stored locally.
 *
 * Navigation preload lets the browser start the page request while the
 * service worker is still waking up, so page switches are not delayed by it.
 */
const CACHE = 'catatan-keuangan-v2';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/icons/icon-192.png', '/icons/favicon-32.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        if (self.registration.navigationPreload) {
            await self.registration.navigationPreload.enable();
        }
        const keys = await caches.keys();
        await Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)));
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode !== 'navigate') return;

    event.respondWith((async () => {
        try {
            const preloaded = await event.preloadResponse;
            if (preloaded) return preloaded;
            return await fetch(event.request);
        } catch (error) {
            return (await caches.match(OFFLINE_URL)) || Response.error();
        }
    })());
});
