/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
const firebaseConfig = {
    apiKey: "AIzaSyCrCzSbX446HoRZ0FPMoFQp9MjXJxvl3iw",
    authDomain: "mood-app-001.firebaseapp.com",
    projectId: "mood-app-001",
    storageBucket: "mood-app-001.appspot.com",
    messagingSenderId: "383271396412",
    appId: "1:383271396412:web:d4e79afe7a9701c67c0f39",
    measurementId: "G-DJ7F95PX1F"
};
firebase.initializeApp(firebaseConfig);

// firebase.initializeApp({
//     apiKey: "XXXX",
//     authDomain: "XXXX.firebaseapp.com",
//     databaseURL: "https://XXXX.firebaseio.com",
//     projectId: "XXXX",
//     storageBucket: "XXXX",
//     messagingSenderId: "XXXX",
//     appId: "XXXX",
//     measurementId: "XXX"
// });

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "/itwonders-web-logo.png",
    };

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});