const CACHE_NAME = 'siternak-ok-v3';

self.addEventListener('install', (event) => {
  event.waitUntil(self.skipWaiting());
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
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('message', (event) => {
  if (!event.data || event.data.type !== 'SHOW_NOTIFICATION') return;

  const payload = event.data.payload || {};
  const title = payload.title || 'SiTernak-OK Gorontalo';
  const options = {
    body: payload.body || 'Terdapat jadwal konfirmasi kebuntingan sapi yang memerlukan tindakan Anda.',
    tag: payload.tag || ('siternak-reminder-' + Date.now()),
    renotify: true,
    requireInteraction: false,
    vibrate: [250, 120, 250, 120, 350],
    data: {
      url: payload.url || '/dashboard#reminder-section'
    }
  };

  if (payload.icon) {
    options.icon = payload.icon;
  }

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

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
    tag: 'siternak-push-' + Date.now(),
    renotify: true,
    vibrate: [250, 120, 250, 120, 350],
    data: {
      url: data.url || '/dashboard#reminder-section'
    }
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

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
