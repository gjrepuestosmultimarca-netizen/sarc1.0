// ============================================================
//  SARC — Service Worker (PWA)
//  Archivo: sarc/sw.js
// ============================================================

const CACHE_NAME = 'sarc-v1';
const ASSETS = [
  '/sarc/',
  '/sarc/index.html',
  '/sarc/css/styles.css',
  '/sarc/js/script.js',
  '/sarc/manifest.json',
  'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js',
  'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js'
];

// Instalación — cachear recursos estáticos
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('[SW] Cacheando recursos...');
      return cache.addAll(ASSETS).catch(err => console.warn('[SW] Error al cachear:', err));
    })
  );
  self.skipWaiting();
});

// Activación — limpiar caches viejos
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch — estrategia: red primero, cache como respaldo
self.addEventListener('fetch', e => {
  // Las llamadas a la API siempre van a la red
  if (e.request.url.includes('/api/')) {
    e.respondWith(
      fetch(e.request).catch(() =>
        new Response(JSON.stringify({ ok: false, error: 'Sin conexión a internet' }), {
          headers: { 'Content-Type': 'application/json' }
        })
      )
    );
    return;
  }

  // Recursos estáticos: cache primero, luego red
  e.respondWith(
    caches.match(e.request).then(cached => {
      if (cached) return cached;
      return fetch(e.request).then(response => {
        if (!response || response.status !== 200) return response;
        const clone = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(e.request, clone));
        return response;
      }).catch(() => caches.match('/sarc/index.html'));
    })
  );
});
