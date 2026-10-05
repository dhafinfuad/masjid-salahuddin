/**
 * Service Worker: Masjid Salahuddin Progressive Web App (PWA)
 * Version: 1.0.0
 * Architecture: Optimized for Laravel 11 & Livewire 3
 */

const CACHE_NAME = 'masjid-salahuddin-v1.0.0';

// Assets to pre-cache on install
const PRECACHE_ASSETS = [
    '/offline',
    '/manifest.json',
    '/favicon.ico',
    '/apple-touch-icon.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/icon-maskable-192x192.png',
    '/images/icons/icon-maskable-512x512.png',
    '/Logo Masjid Salahuddin.webp'
];

// Install Event: Pre-cache core offline assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// Activate Event: Clean up old caches & take control immediately
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// Fetch Event: Smart routing with Livewire safety
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // 1. RULE #1: NEVER cache non-GET requests (e.g. Livewire state updates, form submissions)
    if (request.method !== 'GET') {
        return;
    }

    // 2. RULE #2: ALWAYS bypass caching for Livewire internals, Admin CMS, and Auth routes
    if (
        url.pathname.startsWith('/livewire/') ||
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/auth/')
    ) {
        return;
    }

    // 3. RULE #3: Only handle same-origin or trusted font/CDN requests
    if (url.origin !== self.location.origin && !url.hostname.includes('fonts.googleapis.com') && !url.hostname.includes('fonts.gstatic.com')) {
        return;
    }

    // 4. HTML Page Navigation: Network-First with Offline Fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Try cache for requested URL first
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Fallback to cached offline page
                    const offlinePage = await caches.match('/offline');
                    return offlinePage || new Response('Offline - Perangkat Anda tidak terhubung ke internet.', {
                        headers: { 'Content-Type': 'text/plain; charset=utf-8' }
                    });
                })
        );
        return;
    }

    // 5. Static Assets (CSS, JS, Images, Fonts, Icons): Stale-While-Revalidate
    const isStaticAsset = (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/resources/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.webp') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.endsWith('.woff')
    );

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // Ignore background network error if cachedResponse is available
                });

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }
});
