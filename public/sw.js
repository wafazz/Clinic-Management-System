/**
 * ClinicQo Service Worker
 * Cache-first for static assets, network-first for HTML pages,
 * offline fallback when both fail.
 */

const CACHE_VERSION = 'clinicqo-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;
const OFFLINE_URL = '/offline';

// Pre-cached on install — minimal shell
const PRECACHE_URLS = [
    '/offline',
    '/images/clinicQo.png',
    '/images/icon-192.png',
    '/images/icon-512.png',
    '/star-admin/css/shared/style.css',
    '/star-admin/css/demo_1/style.css',
    '/star-admin/css/enhanced.css',
    '/star-admin/vendors/css/vendor.bundle.base.css',
    '/star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css',
];

// ---- INSTALL ----
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            // Use addAll but tolerate failures (some files may 404 in dev)
            return Promise.all(
                PRECACHE_URLS.map((url) =>
                    cache.add(new Request(url, { cache: 'reload' })).catch(() => null)
                )
            );
        })
    );
    self.skipWaiting();
});

// ---- ACTIVATE ----
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((k) => !k.startsWith(CACHE_VERSION))
                    .map((k) => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ---- FETCH STRATEGY ----
self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    // Only handle same-origin GET requests
    if (req.method !== 'GET' || url.origin !== self.location.origin) return;

    // Never cache POST callbacks, auth, or any path starting with /billplz
    if (url.pathname.startsWith('/billplz/')) return;
    if (url.pathname.startsWith('/login') || url.pathname.startsWith('/logout')) return;

    const isAsset =
        /\.(?:css|js|woff2?|ttf|eot|png|jpe?g|gif|svg|webp|ico)$/.test(url.pathname);

    if (isAsset) {
        // Cache-first for static assets
        event.respondWith(
            caches.match(req).then((cached) => {
                if (cached) return cached;
                return fetch(req)
                    .then((response) => {
                        if (response.ok) {
                            const clone = response.clone();
                            caches.open(RUNTIME_CACHE).then((c) => c.put(req, clone));
                        }
                        return response;
                    })
                    .catch(() => cached);
            })
        );
        return;
    }

    // Network-first for HTML pages, fallback to cache then offline page
    event.respondWith(
        fetch(req)
            .then((response) => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(RUNTIME_CACHE).then((c) => c.put(req, clone));
                }
                return response;
            })
            .catch(() =>
                caches.match(req).then((cached) => cached || caches.match(OFFLINE_URL))
            )
    );
});

// Allow page to message SW to skip waiting on update
self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') self.skipWaiting();
});
