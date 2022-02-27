$(function() {

    // Je récupère les data de la promotion sélectionnée et les envoies en requête HTTP
    $('.promotion').on('click', function() {
        $('.promotion').css('background-color', 'white');
        $(this).css('background-color', '#a8dfa8');
        let promotion = {
            'minAmount'   : $(this).data('min-amount'),
            'reduction'   : $(this).data('reduction'),
            'freeDelivery': $(this).data('free-delivery'),
            'date'        : $(this).data('date'),
            'productCount': $(this).data('product-count'),
            'numberOfUse' : $(this).data('number-of-use'),
            'delivery'    : $(this).data('delivery'),
            'total_ht'    : $(this).data('total_ht'),
            'total_ttc'   : $(this).data('total_ttc')
        };

        $.ajax({
            url: route,
            type: 'GET',
            data: promotion,
            success: function (result) {

                // Si réponse OK je remplace le rendu existant avec les nouvelles valeurs
                $('.include_total_table').html(result.view);
                // et je remplace la valeur du total ttc dans mon formulaire vers la page de paiement
                $('#total_ttc').val(result.total_ttc)
            },
            error: function (err) {
                console.log(err);
            }
        });
    });
});