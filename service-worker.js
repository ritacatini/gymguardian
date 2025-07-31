const CACHE_NAME = 'gestantes-cache-v1';
const urlsToCache = [
    '/',
    '/index.html',
    '/style.css',
    '/scripts.js',
    '/manifest.json',
    '/icon-256.png',
    '/icon-512.png'
];

// Instala o Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('Arquivos em cache');
            return cache.addAll(urlsToCache);
        })
    );
});

// Responde com recursos do cache
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});

// Atualiza o cache
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
