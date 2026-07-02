importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');

importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

const firebaseConfig = {
    apiKey: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
    authDomain: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
    projectId: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
    storageBucket: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
    messagingSenderId: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
    appId: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
    measurementId: "4f4d1b38e94e5ca4cb5cb3b5b4e93f01f59e0a35123",
};


if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {

    let title = payload.data.title;

    let options = {
        body: payload.data.body,
        icon: payload.data.icon,
        data: {
            time: new Date(Date.now()).toString(),
            click_action: payload.data.click_action
        }
    };

    return self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', function (event) {
    let action_click = event.notification.data.click_action;
    event.notification.close();

    event.waitUntil(
        clients.openWindow(action_click)
    );
});
;
