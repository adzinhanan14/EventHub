// AmikomEventHub - Service Worker
// Strategi:
// - Halaman dinamis (Blade, ada CSRF/session) -> network-first, TIDAK di-cache isi HTML-nya
//   supaya data event & form checkout selalu fresh dan token CSRF tidak basi.
// - Asset statis (css/js/gambar/font/icon) -> cache-first, biar app buka lebih cepat & bisa
//   dipakai walau koneksi lemot/putus sebentar.
// - Halaman sensitif (checkout, payment, admin, organizer) -> selalu network-only,
//   tidak pernah disentuh service worker ini sama sekali.

const CACHE_NAME = 'eventhub-static-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/manifest.json',
    '/assets/logo-amikom.png',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// Path yang service worker ini tidak boleh ikut campur sama sekali (biarkan selalu langsung ke network)
const NEVER_INTERCEPT = [
    '/checkout',
    '/payment',
    '/payment-finish',
    '/admin',
    '/organizer',
    '/ticket/scan',
    '/logout',
    '/auth/google',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

function isNeverIntercepted(url) {
    return NEVER_INTERCEPT.some((path) => url.pathname.startsWith(path));
}

function isStaticAsset(url) {
    return (
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/build/') || // hasil build Vite
        /\.(css|js|png|jpg|jpeg|svg|webp|gif|woff2?|ttf|ico)$/.test(url.pathname)
    );
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Hanya urus request same-origin dengan method GET
    if (url.origin !== self.location.origin || request.method !== 'GET') {
        return;
    }

    if (isNeverIntercepted(url)) {
        return; // biarkan browser handle langsung, tanpa service worker
    }

    // Navigasi halaman (buka URL / klik link) -> network-first + fallback offline page
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Asset statis -> cache-first, update cache di background
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const fetchPromise = fetch(request)
                    .then((networkResponse) => {
                        if (networkResponse && networkResponse.ok) {
                            const clone = networkResponse.clone();
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                        }
                        return networkResponse;
                    })
                    .catch(() => cached);
                return cached || fetchPromise;
            })
        );
    }
    // Selain itu (mis. request dinamis lain) -> biarkan default, tidak dicache
});
