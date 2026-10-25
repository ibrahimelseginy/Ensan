// Service Worker for Ensan System Notifications
self.addEventListener('install', function (event) {
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  event.waitUntil(self.clients.claim());
});

// Handle incoming push events (Web Push API)
self.addEventListener('push', function (event) {
  var data = { title: 'مؤسسة إنسان للأعمال الخيرية', body: 'لديك إشعار جديد في لوحة التحكم', link: '/notifications' };
  if (event.data) {
    try {
      var parsed = event.data.json();
      data = Object.assign(data, parsed);
    } catch (e) {
      data.body = event.data.text();
    }
  }

  var options = {
    body: data.body,
    icon: '/images/logo.png',
    badge: '/images/logo.png',
    tag: data.tag || ('ensan_' + Date.now()),
    renotify: true,
    requireInteraction: false,
    data: {
      link: data.link || '/notifications'
    }
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'مؤسسة إنسان للأعمال الخيرية', options)
  );
});

// Handle notification click event: focus existing tab or open new tab
self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  var targetUrl = (event.notification.data && event.notification.data.link) ? event.notification.data.link : '/notifications';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
      for (var i = 0; i < clientList.length; i++) {
        var client = clientList[i];
        if (client.url && 'focus' in client) {
          if (targetUrl && client.navigate) {
            client.navigate(targetUrl);
          }
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
