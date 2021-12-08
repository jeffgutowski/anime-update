$('.check3s').on('click', function() {
    if (!$(this).hasClass("disabled")) {
        if ($(this).hasClass('positive')){
            $(this).removeClass('positive fa fa-check fa1');
            $(this).addClass('negative fa fa-times fa1');
        } else if ($(this).hasClass('negative')){
            $(this).removeClass('negative fa fa-times fa1');
        } else {
            $(this).addClass('positive fa fa-check fa1');
        }
    }
});
$('.check3s-label').on('click', function () {
    $(this).parent().find('.check3s').click()
})
