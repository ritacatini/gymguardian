let deferredPrompt;
const installBtn = document.getElementById('installBtn');

window.addEventListener('beforeinstallprompt', (e) => {
    // Impede que o prompt seja exibido automaticamente
    e.preventDefault();
    deferredPrompt = e;
    installBtn.hidden = false;

    installBtn.addEventListener('click', () => {
        installBtn.hidden = true;
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('App instalado');
            } else {
                console.log('App não instalado');
            }
            deferredPrompt = null;
        });
    });
});
