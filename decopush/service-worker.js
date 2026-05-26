/**
 * =============================================
 * Service Worker - DECOMOBIL
 * Notificaciones push en segundo plano
 * =============================================
 */

const CACHE_NAME = 'decomobil-v1';

// Instalación del Service Worker
self.addEventListener('install', (event) => {
  console.log('[SW] Installing...');
  self.skipWaiting();
});

// Activación del Service Worker
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating...');
  event.waitUntil(self.clients.claim());
});

// Manejo de notificaciones push
self.addEventListener('push', (event) => {
  console.log('[SW] Push notification received:', event);
  
  if (event.data) {
    try {
      const data = event.data.json();
      
      const options = {
        body: data.body || 'Nueva notificación',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: data.tag || 'notification',
        requireInteraction: false,
        data: data.data || {}
      };
      
      event.waitUntil(
        self.registration.showNotification(data.title || 'DECOMOBIL', options)
      );
    } catch (e) {
      console.error('[SW] Error parsing push:', e);
      event.waitUntil(
        self.registration.showNotification('DECOMOBIL', {
          body: event.data.text(),
          icon: '/favicon.ico'
        })
      );
    }
  }
});

// Manejo de clicks en notificaciones
self.addEventListener('notificationclick', (event) => {
  console.log('[SW] Notification clicked:', event);
  event.notification.close();
  
  const data = event.notification.data || {};
  const clientData = {
    type: 'NOTIFICATION_CLICKED',
    ticketId: data.ticketId,
    newsId: data.newsId,
    timestamp: Date.now()
  };
  
  event.waitUntil(
    self.clients.matchAll({ type: 'window' }).then((clients) => {
      // Si existe una ventana abierta, enviar mensaje
      for (let i = 0; i < clients.length; i++) {
        if (clients[i].url.includes('home.php') || clients[i].url.includes('admin.php')) {
          return clients[i].postMessage(clientData);
        }
      }
      // Si no hay ventana, abrir una
      if (self.clients.openWindow) {
        return self.clients.openWindow('/src/home.php');
      }
    })
  );
});

// Escuchar mensajes desde el cliente
self.addEventListener('message', (event) => {
  console.log('[SW] Message received:', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
