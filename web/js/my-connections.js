$(document).on('click', 'button.disconnect', function (e) {
    var button = $(e.target),
        form = $('form.confirmation-container');

    button.hide(200, function(){
        form.show(200);
    });
});

$(document).on('change', 'input.confirmation', function(e) {
    var checkbox = $(e.target),
       button = $('button.confirm-button');

    if (checkbox.is(':checked') === true) {
       button.attr('disabled', false);
    } else {
       button.attr('disabled', true);
    }
});
