import './bootstrap';

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(() => {
                console.log('Service worker enregistré avec succès.');
            })
            .catch((error) => {
                console.error('Erreur lors de l\'enregistrement du service worker:', error);
            });
    });
}
