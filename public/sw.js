// ZBIZ+ PWA Service Worker v1.0.23 (Local Assets & Safe Offline Cache)
const CACHE_NAME = 'zbiz-pwa-v2';
const STATIC_ASSETS = [
    '/favicon.png',
    '/favicon.ico',
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

    // Regra estrita de segurança: NUNCA armazenar em cache requisições não-GET,
    // transações financeiras, endpoints de sync ou chamadas da API
    if (request.method !== 'GET' || 
        request.url.includes('/api/') || 
        request.url.includes('/pos/sale') ||
        request.url.includes('/cash/') ||
        request.url.includes('/sales/') ||
        request.url.includes('/login') ||
        request.url.includes('/logout')) {
        return;
    }

    // Para páginas de navegação HTML: Network First sem armazenar HTML autenticado no cache
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match('/favicon.png');
            })
        );
        return;
    }

    // Para assets locais estáticos compilados em /build/ (CSS, JS, Fontes WOFF2, Imagens)
    if (request.url.includes('/build/') || 
        request.destination === 'style' || 
        request.destination === 'script' || 
        request.destination === 'font' || 
        request.destination === 'image') {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => cachedResponse);
                });
            })
        );
    }
});
