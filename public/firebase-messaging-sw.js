importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-auth.js');

firebase.initializeApp({
    apiKey: "AIzaSyDoiOgCoCszkhiCy_TAkKoSC1DWM57ejis",
    authDomain: "valley-f2ea1.firebaseapp.com",
    projectId: "valley-f2ea1",
    storageBucket: "valley-f2ea1.firebasestorage.app",
    messagingSenderId: "115688405848",
    appId: "1:115688405848:web:3e2a732dd5f3b49504432d",
    measurementId: "G-THW2HQKJZG"
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    return self.registration.showNotification(payload.data.title, {
        body: payload.data.body || '',
        icon: payload.data.icon || ''
    });
});