const CACHE_NAME = 'sutolog-cache-v1';
const urlsToCache = [
  '/',
  '/manifest.json',
  '/css/app.css',
  '/js/app.js',
  '/icons/icon-192.png',
  '/icons/icon-512.png'
];

// インストール時にキャッシュする
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(urlsToCache);
    })
  );
});

// フェッチ時のキャッシュ戦略
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
