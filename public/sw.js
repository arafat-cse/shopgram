self.addEventListener('push', function (event) {
    if (!event.data) return;

    const data = event.data.json();

    event.waitUntil(
        self.registration.showNotification(data.title || 'ShopGram', {
            body:    data.body    || '',
            icon:    data.icon   || '/images/logo.png',
            badge:   '/images/logo.png',
            data:    { url: data.action || '/' },
            actions: data.action ? [{ action: 'open', title: 'Open' }] : [],
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
            for (const client of list) {
                if (client.url === url && 'focus' in client) return client.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});
