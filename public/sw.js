const CACHE_NAME = 'siternak-ok-v2';
const ASSETS_TO_CACHE = [
  'https://cdn.tailwindcss.com',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Listener untuk menampilkan notifikasi di semua peramban ponsel (Android/iOS/Mobile Browser) & Desktop
self.addEventListener('message', (event) => {
  if (!event.data || event.data.type !== 'SHOW_NOTIFICATION') return;

  const payload = event.data.payload || {};
  const title = payload.title || 'SiTernak-OK Gorontalo';
  const options = {
    body: payload.body || 'Terdapat jadwal konfirmasi kebuntingan sapi yang memerlukan tindakan Anda.',
    icon: payload.icon || 'https://cdn-icons-png.flaticon.com/512/2395/2395796.png',
    badge: payload.badge || 'https://cdn-icons-png.flaticon.com/512/2395/2395796.png',
    tag: payload.tag || 'siternak-reminder-' + Date.now(),
    renotify: true,
    requireInteraction: payload.requireInteraction !== undefined ? payload.requireInteraction : false,
    vibrate: [200, 100, 200, 100, 300],
    data: {
      url: payload.url || '/dashboard#reminder-section'
    },
    actions: [
      {
        action: 'open',
        title: 'Buka & Konfirmasi'
      },
      {
        action: 'dismiss',
        title: 'Tutup'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Listener Push Event standar Web Push API
self.addEventListener('push', (event) => {
  let data = {};
  if (event.data) {
    try {
      data = event.data.json();
    } catch (e) {
      data = { body: event.data.text() };
    }
  }

  const title = data.title || 'SiTernak-OK - Pengingat Lapangan';
  const options = {
    body: data.body || 'Ada sapi betina yang memasuki batas konfirmasi 3 hari.',
    icon: 'https://cdn-icons-png.flaticon.com/512/2395/2395796.png',
    badge: 'https://cdn-icons-png.flaticon.com/512/2395/2395796.png',
    vibrate: [200, 100, 200, 100, 300],
    data: {
      url: data.url || '/dashboard#reminder-section'
    }
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Saat notifikasi diketuk di layar HP / Peramban Ponsel
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'dismiss') {
    return;
  }

  const targetUrl = (event.notification.data && event.notification.data.url)
    ? event.notification.data.url
    : '/dashboard#reminder-section';

  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if ('focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      if (self.clients.openWindow) {
        return self.clients.openWindow(targetUrl);
      }
    })
  );
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  // Jangan cache endpoint dinamis / polling autentikasi & notifikasi
  if (url.pathname.startsWith('/notifications') || url.pathname.startsWith('/login') || url.pathname.startsWith('/logout')) {
    return;
  }

  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});
