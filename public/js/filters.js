$(function () {
    window.removeFilter = function(span, type, id) {
        span.remove()
        if (id !== undefined) {
            delete window.filters[type][id]
        }
    }

    function findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }
    function csvToObj(csv) {
        if (csv === null) {
            return {}
        }
        let array = csv.split(',')
        let obj = {};
        for (let i = 0; i < array.length; i++) {
            obj[array[i]] = array[i]
        }
        return obj
    }
    $(document).ready(function(){
        var filters = {
            // age rating
            a: csvToObj(findGetParameter('a')),
            // company
            c: csvToObj(findGetParameter('c')),
            // condition
            co: csvToObj(findGetParameter('co')),
            // developers
            d: csvToObj(findGetParameter('d')),
            // fragment
            frag: findGetParameter('frag'),
            // genres
            g: csvToObj(findGetParameter('g')),
            // genre exclusivity
            ge: findGetParameter('ge') !== null ? findGetParameter('ge') : "e",
            // order
            o: findGetParameter('o'),
            // search terms
            q: findGetParameter('q'),
            // platforms
            p: csvToObj(findGetParameter('p')),
            // price range
            pr: csvToObj(findGetParameter('pr')),
            // page
            page: findGetParameter('page'),
            // publishers
            pu: csvToObj(findGetParameter('pu')),
            // rating
            r: findGetParameter('r'),
            // rating custom
            rc: csvToObj(findGetParameter('rc')),
            // region
            re: findGetParameter('re'),
            // rating show
            rs: findGetParameter('rs'),
            // product type
            t: csvToObj(findGetParameter('t')),
            // release year
            ys: findGetParameter('ys'),
            ye: findGetParameter('ye')
        };
        window.filters = filters

        $('.region-change').change(function () {
            window.location.replace('/region/'+ $(this).val())
        })

        $('#order_by').change(function () {
            window.filters.o = $("#order_by").val()
            window.filters.page = 1
            window.filterRedirect()
        });

        $('#order-direction-btn').click(function (e) {
            e.preventDefault();
            var direction = ''
            if ($('#order-direction').hasClass('fa-sort-amount-up')) {
                direction = '-'
            }

            var order = $("#order_by").val()

            window.filters.page = 1
            window.filters.o = direction + order.replace('-', '')
            window.filterRedirect()
        });



        $("#developer-search").on("keyup", debounce(function(){
            var typedValue = $(this).val()
            $("#developers option").each(function(i, item) {
                if (typedValue === $(this).val()) {
                    $("#developer-search").val('');
                    let selectedValue = $(this).attr('data-value')
                    let selectedName = $(this).val()
                    $("#selected-developers").append($('<span onclick="removeFilter(this, \'d\', '+selectedValue+')" data-value="'+selectedValue+'" class="clickable label platform-label platform-filter m-r-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i> '+selectedName+'</span>'))
                    window.filters.d[selectedValue] = selectedValue
                    return;
                }
            })
            $("#developers").empty();
            $.ajax({
                url: "/api/developers/active",
                data: {q: typedValue},
                success: function (response) {
                    $.each(response.data, function(i, item){
                        $("#developers").append($("<option>").attr('data-value', item.id).text(item.name));
                    })
                }
            });
        }, 500));

        $("#publisher-search").on("keyup", debounce(function(){
            var typedValue = $(this).val()
            $("#publishers option").each(function(i, item) {
                if (typedValue === $(this).val()) {
                    $("#publisher-search").val('');
                    let selectedValue = $(this).attr('data-value')
                    let selectedName = $(this).val()
                    $("#selected-publishers").append($('<span onclick="removeFilter(this, \'pu\', '+selectedValue+')" data-value="'+selectedValue+'" class="clickable label platform-label platform-filter m-r-5 m-l-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i> '+selectedName+'</span>'))
                    window.filters.pu[selectedValue] = selectedValue
                    return;
                }
            })
            $("#publishers").empty();
            $.ajax({
                url: "/api/publishers/active",
                data: {q: typedValue},
                success: function (response) {
                    $.each(response.data, function(i, item){
                        $("#publishers").append($("<option>").attr('data-value', item.id).text(item.name));
                    })
                }
            });
        }, 500));

        $("#company-search").on("keyup", debounce(function(){
            var typedValue = $(this).val()
            $("#companies option").each(function(i, item) {
                if (typedValue === $(this).val()) {
                    $("#company-search").val('');
                    let selectedValue = $(this).attr('data-value')
                    let selectedName = $(this).val()
                    $("#selected-companies").append($('<span onclick="removeFilter(this, \'c\', '+selectedValue+')" data-value="'+selectedValue+'" class="clickable label platform-label platform-filter m-r-5 m-l-5 m-b-5 inline-block platform-filter-active"><i class="fas fa-times-circle search-select"></i> '+selectedName+'</span>'))
                    window.filters.c[selectedValue] = selectedValue
                    return;
                }
            })
            $("#companies").empty();
            $.ajax({
                url: "/api/companies/search",
                data: {q: typedValue},
                success: function (response) {
                    $.each(response.data, function(i, item){
                        $("#companies").append($("<option>").attr('data-value', item.id).text(item.name));
                    })
                }
            });
        }, 500));
        $('.platform-filter').click(function(e) {
            let platform = $(this).attr('data-value')
            e.preventDefault();
            $(this).toggleClass('platform-filter-active')
            if ($(this).hasClass('platform-filter-active')) {
                $(this).css('background-color', $(this).data('color') );
                window.filters.p.push(platform)
            } else {
                $(this).css('background-color', '');
                platformIndex = filters.p.toString().indexOf(platform)
                if (platformIndex >= 0) {
                    window.filters.p.splice(platformIndex, 1)
                }
            }
        });

        $('#search-terms').keyup(function() {
            // if the search term changes then order by relevance
            if ($(this).val().length > 0) {
                filters.o = "-rank"
            } else {
                var order = $("#order_by").val()
                var direction = ""
                if (order.includes("rank")) {
                    order = ""
                } else if ($('#order-direction').hasClass('fa-sort-amount-down')) {
                    direction = '-'
                }
                filters.o = direction + order
            }
        });

        $('#filter-submit').click(function(e)
        {
            e.preventDefault();
            $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            $(this).addClass('loading')

            window.filters.a = {};
            $(".age-filter-checkbox").each(function() {
                if ($(this).prop("checked") === true) {
                    window.filters.a[$(this).val()] = $(this).val()
                }
            })
            window.filters.p = {};
            $(".platform-checkbox").each(function() {
                if ($(this).prop("checked") === true) {
                    window.filters.p[$(this).val()] = $(this).val()
                }
            })
            window.filters.g = {};
            $(".genre-checkbox").each(function() {
                if ($(this).hasClass("positive")) {
                    window.filters.g[$(this).attr("data-value")] = $(this).attr("data-value")
                } else if ($(this).hasClass("negative")) {
                    window.filters.g[-$(this).attr("data-value")] = -$(this).attr("data-value")
                }
            })
            window.filters.t = {};
            $(".types-checkbox").each(function() {
                if ($(this).prop("checked") === true) {
                    filters.t[$(this).val()] = $(this).val()
                }
            })
            window.filters.r = $("input[name='star-rating']:checked").val();
            window.filters.rc = ""
            if (window.filters.r == "custom") {
                window.filters.rc = $("#rating-min").val() + "-" + $("#rating-max").val()
                if (window.filters.rc == "-") {
                    window.filters.rc = ""
                    window.filters.r = null
                }
            }

            window.filters.pr = $("#price-min").val() + "-" + $("#price-max").val()
            if (window.filters.pr == "-" || window.filters.pr == 'undefined-undefined') {
                window.filters.pr = null;
            }

            window.filters.co = {};
            $(".condition-filter").each(function() {
                if ($(this).prop("checked") === true) {
                    filters.co[$(this).val()] = $(this).val()
                }
            })

            window.filters.ys = null;
            if ($("#release_year_start").val() !== "") {
                window.filters.ys = $("#release_year_start").val();
            }
            window.filters.ye = null;
            if ($("#release_year_end").val() !== "") {
                window.filters.ye = $("#release_year_end").val();
            }
            let search_terms = $("#search-terms").val()
            window.filters.q = search_terms ? search_terms : null;
            window.filters.ge = $("#genre-exclusive").val()
            window.filters.page = null;
            window.filterRedirect()
        });
        $('#remove-filter').click(function (e) {
            e.preventDefault();
            window.location.replace(window.location.pathname)
        });
        $('.filter-header').click(function(){
            let body = $(this).parent().find('.filter-body')
            if (body.is(':hidden')) {
                let chev = $(this).find('.fa-plus').removeClass('fas fa-plus').addClass('fas fa-minus')
                body.slideDown()
            } else {
                let chev = $(this).find('.fa-minus').removeClass('fas fa-minus').addClass('fas fa-plus')
                body.slideUp()
            }
        })
        for(i = 1; i < 5; i++) {
            $("#star-"+i).rating({
                value: i,
                stars: 5,
                half: true,
                readonly: true,
                color: "orange",
                click: function () {
                    return
                }
            });
        }
        $("#rating-custom").change(function() {
            $(".rating-range").show();
        });
        $(".star-radio").click(function() {
            $(".rating-range").hide();
        });
        // Always show one decimal point for custom rating min and max
        $("#rating-min, #rating-max").change(function () {
            $(this).val(parseFloat($(this).val()).toFixed(1))
        });
        // Always show two decimal point for price min and max
        $("#price-min, #price-max").change(function () {
            $(this).val(parseFloat($(this).val()).toFixed(2))
        });
        // limit start release year to be less then release end year
        $("#release_year_start").on("change", function () {
            let selected_end =  $("#release_year_end").val()
            let start = $(this).val() ? $(this).val() : 1977;
            let end = new Date().getFullYear() + 2;
            let options = {}
            for(year = start; year <= end; year++) {
                options[year] = year;
            }
            let year_end = $("#release_year_end")
            year_end.empty()
            year_end.append($("<option value=''></option>"))
            $.each(options, function(key,value) {
                if (value == selected_end) {
                    year_end.append($("<option selected></option>").attr("value", value).text(key));
                } else {
                    year_end.append($("<option></option>").attr("value", value).text(key));
                }
            });
        });
        // limit release end year to be greater the release start year
        $("#release_year_end").on("change", function () {
            let selected_start = $("#release_year_start").val()
            let start = 1977;
            let end = $("#release_year_end").val() ? $("#release_year_end").val() : new Date().getFullYear() + 2;
            let options = {}
            for(year = start; year <= end; year++) {
                options[year] = year;
            }
            let year_start = $("#release_year_start")
            year_start.empty()
            year_start.append($("<option value=''></option>"))
            $.each(options, function(key,value) {
                if (value == selected_start) {
                    year_start.append($("<option selected></option>").attr("value", value).text(key));
                } else {
                    year_start.append($("<option></option>").attr("value", value).text(key));
                }
            });
        });

        $(".fltr-btn").on("click", function () {
            window.filters.re = null
            if($(this).attr('data-value') != 'all') {
                window.filters.re = $(this).attr('data-value');
            }
            window.filterRedirect()
        });

        $(".ratings-btn").on("click", function () {
            window.filters.rs = null
            if($(this).attr('data-value') != 'site') {
                window.filters.rs = $(this).attr('data-value');
            }
            var order_by = $("#order_by").val()
            if (order_by == "rating" || order_by == "difficulty" || order_by == "duration") {
                var dir = window.filters.o.charAt(0) == "-" ? "-" : "";
                if (window.filters.rs === "my") {
                    window.filters.o = dir + order_by
                } else {
                    window.filters.o = dir + "average_" + order_by
                }
            }
            window.filterRedirect()
        });

        window.filterRedirect = function () {
            let params = ""
            $.each(window.filters, function (filter, value) {
                if (value !== null && value !== undefined) {
                    if (value.constructor === Object && Object.keys(value).length !== 0) {
                        params += "&" + filter + "=" + Object.keys(value)
                    } else if (typeof value === "string" && value.length > 0 && filter !== "ge") {
                        params += "&" + filter + "=" + value
                    } else if (filter == "ge" && value !== "e") {
                        params += "&" + filter + "=" + value
                    }
                }
            });
            params = params.replace("&", "?")
            var hash = $(location).attr('hash') ? $(location).attr('hash') : "";
            window.location.replace(window.location.pathname + params + hash)
        }
    });
});
