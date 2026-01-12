const CACHE_NAME = 'vestalize-v1';
const ASSETS = [
    '/css/app.css',
    '/js/app.js',
    '/img/logo.png',
    '/offline'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(ASSETS);
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        }).catch(() => {
            if (event.request.mode === 'navigate') {
                return caches.match('/offline');
            }
        })
    );
});
