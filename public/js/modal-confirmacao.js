$(document).ready(function () {
    // Quando o modal for aberto
    $('#modalConfirmacao').on('show.bs.modal', function (event) {
        const botao = $(event.relatedTarget); // Botão que acionou o modal
        const url = botao.data('url'); // Obtém o valor do atributo data-url

        // Define o link do botão de confirmação dinamicamente
        $('#btnConfirmarRemocao').attr('href', url);
    });
});