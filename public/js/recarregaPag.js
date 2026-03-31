function baixarXml() {
    document.getElementById('downloadFrame').src = '/rota-download';

    setTimeout(() => {
        window.location.reload();
    }, 1900);
}