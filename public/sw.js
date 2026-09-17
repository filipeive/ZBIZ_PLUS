// ZBIZ+ PWA Service Worker v1.0.22
const CACHE_NAME = 'zbiz-pwa-v1';
const STATIC_ASSETS = [
    '/favicon.png',
    '/apple-touch-icon.png',
    '/manifest.json'
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('Erro ao pré-armazenar assets no cache:', err);
            });
        })
    );
});

// Ativação e limpeza de caches antigos
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
        }).then(() => self.clients.claim())
    );
});

// Interceção de Requisições
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Ignorar requisições não GET ou endpoints de sync/transações
    if (request.method !== 'GET' || 
        request.url.includes('/api/sync/') || 
        request.url.includes('/pos/sale') ||
        request.url.includes('/api/ping')) {
        return;
    }

    // Para páginas de navegação HTML: Network First com fallback de cache
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match(request).then((response) => {
                    return response || caches.match('/favicon.png');
                });
            })
        );
        return;
    }

    // Para arquivos estáticos (CSS, JS, Imagens, Fontes): Stale-While-Revalidate
    if (request.destination === 'style' || 
        request.destination === 'script' || 
        request.destination === 'image' || 
        request.destination === 'font') {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    const fetchPromise = fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => cachedResponse);

                    return cachedResponse || fetchPromise;
                });
            })
        );
    }
});

