$(function () {
    function saveListItem(fields) {
        let request = new FormData();
        request.append('custom_list_id', $("#id").val());
        request.append('custom_list_item_id', fields.find('.item_id').val());
        request.append('description', fields.find('.description').val());
        request.append('order_number', fields.find('.order_number').val());
        if (fields.find('.custom_thumbnail')[0].files[0]) {
            request.append('thumbnail', fields.find('.custom_thumbnail')[0].files[0]);
        }
        fields.find(".saving").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
        $.ajax({
            url: "/custom-lists/items/save",
            data: request,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
            type: 'POST',
            success: function (response) {
                setTimeout(function () {
                    fields.find(".saving").html('<i class="fa fa-save"></i>')
                    setTimeout(function () {
                        fields.find(".saving").html('')
                    }, 3000)
                }, 1000)

            }
        });
    }
    function moveItem(item) {
        var new_number = $(item).val()
        if (new_number < 1) {
            new_number = 1;
            $(item).val(1)
        }
        list_item = $(item).parents("li")
        var max = $('#list-items').find('li').length - 1
        var count = 1
        list_item.slideUp(500, function (){
            if (new_number > max) {
                new_number = max + 1
                $(item).val(new_number)
            }
            list_item.remove()
            $('#list-items').find('li').each(function(item) {
                let order_number = $(this).find('.order_number')
                if (new_number == count) {
                    $(this).before(list_item)
                    saveListItem(list_item)
                    count++;
                }
                if (order_number.val() != count) {
                    order_number.val(count)
                    saveListItem($(this))
                }
                if (count + 1 == new_number) {
                    $(this).after(list_item)
                    $(list_item).addClass('save-list-item')
                    saveListItem(list_item)
                }
                count++;
            })
            list_item.slideDown()
        })

    }

    function moveList(list) {
        var new_number = $(list).val()
        if (new_number < 1) {
            new_number = 1;
            $(list).val(1)
        }
        var li = $(list).parents("li")
        var max = $('.cl-ul').find('li').length - 1
        var count = 1
        li.slideUp(0, function (){

            if (new_number > max) {
                new_number = max + 1
                $(list).val(new_number)
            }
            li.remove()
            $('.cl-ul').find('li').each(function() {
                let order_number = $(this).find('.cl-order-number')
                if (new_number == count) {
                    $(this).before(li)
                    saveCL(li)
                    count++;
                }
                if (order_number.val() != count) {
                    order_number.val(count)
                    saveCL($(this))
                }
                if (count + 1 == new_number) {
                    $(this).after(li)
                    saveCL(li)
                }
                count++;
            })
            li.slideDown(0)
        })
    }

    function saveCL(list) {
        let request = new FormData();
        request.append('id', $(list).find('.cl-id').val());
        request.append('order_number', $(list).find('.cl-order-number').val());
        list.find(".saving").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
        $.ajax({
            url: "/custom-lists/save/arrange",
            data: request,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
            type: 'POST',
            success: function (response) {
                setTimeout(function () {
                    list.find(".saving").html('<i class="fa fa-save"></i>')
                    setTimeout(function () {
                        list.find(".saving").html('')
                    }, 3000)
                }, 1000)
            }
        });
    }


    $(document).on("keyup keydown change", ".custom-list", function () {
        $('#save-list').removeAttr('disabled')
    });

    $(document).on("keyup keydown change", ".custom-list", debounce(function(){
        if ($('#id').val()) {
            window.saveList()
        }
    }, 1800));

    $(document).on("keyup keydown", ".order_number", debounce(function(){
        moveItem(this)
    }, 1800));

    $(document).on("keyup keydown", ".cl-order-number", debounce(function(){
        moveList(this)
    }, 1800));

    $(document).ready(function() {
        window.saveListItem = function(item) {
            saveListItem(item)
        }
        $(".save-list-item").on("keyup", debounce(function(){
            fields = $(this).parent().parent().parent().parent()
            saveListItem(fields)
        }, 1800));


        $("#list-items").on( "sortupdate", function(event, ui) {
            var count = 1
            $(this).find('li').each(function(item) {
                let order_number = $(this).find('.order_number')
                if (order_number.val() != count) {
                    order_number.val(count)
                    saveListItem($(this))
                }
                count++;
            })
        });


        $(".cl-ul").sortable({
            opacity: .3,
            cursor: "pointer",
            placeholder: "cl-placeholder",
            tolerance: "pointer",
            scroll: true,
            scrollSpeed: 500,
        });
        $(".cl-ul").on("sortupdate", function(event, ui) {
            var count = 1
            $(this).find('li').each(function(list) {
                // console.log('list', $(list), $(this))
                let order_number = $(this).find('.cl-order-number')
                if (order_number.val() != count) {
                    order_number.val(count)
                    saveCL($(this))
                }
                count++;
            })
        });



    });
});