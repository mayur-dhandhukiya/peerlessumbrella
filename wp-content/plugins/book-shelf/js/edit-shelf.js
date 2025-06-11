(function($) {

    $(document).ready(function() {


        // $(".if-js-closed").removeClass("if-js-closed").addClass("closed");
                   
        // postboxes.add_postbox_toggles( 'bookshelf');
        



        $('#edit-shelf').show()

        var action = getParameterByName('action')
        var $form = $('#shelf-options-form')

        $('.creating-page').hide()

        options = $.parseJSON(window.shelf)

        convertStrings(options)

        $(".ui-sortable").sortable();

        var $btnSelectFlipbooks = $('.book-shelf-button-select')
        var $previewCovers = $('.book-shelf-preview-covers')

        if (typeof options.covers != 'undefined') {

            for (var i = 0; i < options.covers.length; i++) {
                var cover = options.covers[i]
                var id = cover.id
                var src = $('#img-' + id).attr('src')

                createCover(id, src, i)

            }
        }

        function createCover(id, src, index) {

            var $cover = $('<div id="' + id + '" class="cover"><img src="' + src + '"></div>').appendTo($previewCovers)
            // var inputSrc = $('<input type="hidden" name="covers['+index+'][src]">').val(src).appendTo($cover)
            var $deleteCover = $('<span>')
                .addClass('delete-cover dashicons dashicons-no-alt')
                .appendTo($cover)
                .hide()
                .click(function() {
                    $(this).parent().remove()
                })
            // var inputId = $('<input type="hidden" name="covers['+index+'][id]">').val(id).appendTo($cover)
            $cover.bind('mouseover', function() {
                $(this).find('.delete-cover').show()
            })
            $cover.bind('mouseout', function() {
                $(this).find('.delete-cover').hide()
            })

        }

        function applyOptions() {
            $('#shelf-options-form input').trigger('change')
            $('#shelf-options-form select').trigger('change')
        }

        var $img = $('#shelf-image')
        $img.attr('src', options.image)
        var inputSrc = $('<input type="hidden" name="image">').val(options.image).appendTo($form)

        $btnSelectFlipbooks.click(function(e) {

            var list = []
            $('.row-checkbox').each(function() {
                // console.log(this)
                if ($(this).is(':checked')) {
                    list.push($(this).attr('name'))
                    // console.log(this.parentNode)
                }
            })

            var numCovers = $('.cover').length

            if (list.length > 0) {
                for (var i = 0; i < list.length; i++) {
                    var item = list[i]
                    var index = numCovers + i
                    var id = list[i]
                    var src = $('#img-' + id).attr('src')
                    createCover(id, src, index)
                }

                applyOptions()
            }

            $("#TB_closeWindowButton").click()

        })

        var title
        if (options.name) {
            title = 'Edit "' + stripslashes(options.name) + '"'
        } else {
            title = 'Add New Shelf'
        }

        $("#edit-shelf-text").text(title)

        $('.select-shelf-image').click(function(e) {
            e.preventDefault();

            var $img = $('#shelf-image')

            var uploader = wp.media({
                title: 'Select file',
                button: {
                    text: 'Select'
                },
                multiple: false // Set this to true to allow multiple files to be selected
            }).on('select', function() {

                // $('.unsaved').show()
                var arr = uploader.state().get('selection');
                var selected = arr.models[0].attributes.url

                // $input.val(selected)
                $img.attr('src', selected)
                var inputSrc = $('<input type="hidden" name="image">').val(selected).appendTo($form)

            }).open();
        })

        function selectShelfImage(){
            var $img = $('#shelf-image')

            var uploader = wp.media({
                title: 'Select file',
                button: {
                    text: 'Select'
                },
                multiple: false // Set this to true to allow multiple files to be selected
            }).on('select', function() {

                // $('.unsaved').show()
                var arr = uploader.state().get('selection');
                var selected = arr.models[0].attributes.url

                // $input.val(selected)
                $img.attr('src', selected)
                var inputSrc = $('<input type="hidden" name="image">').val(selected).appendTo($form)

            }).open();
        }

        var defaults = {
            'shelfImage': 'wood',
            'shelfImageMarginTop': '-12',
            'shelfBg': '',
            'shelfAlign': 'center',
            'shelfPadding': '0',

            'coverSize': '12',
            'coverMaxWidth': '100',
            'coverMarginH': '10',
            'coverMarginV': '0',
            'coversAlign': 'center',

            'coverShadow': '1px 1px 2px rgba(0,0,0,.2)'
        }

        addOption("settings", "name", "text", "Shelf name");

        addOption("settings", "shelfImage", "dropdown", "Shelf image", ['wood', 'glass', 'metal', 'custom', 'no shelf']);
        addOption("settings", "shelfImageMarginTop", "number", "Shelf image offset", '', 'Margin-top', 'px');
        addOption("settings", "shelfBg", "color", "Shelf background color", '');
        addOption("settings", "shelfAlign", "dropdown", "Shelf align", ['right', 'left', 'center'], 'Horizontal align of shelf image');
        addOption("settings", "shelfPadding", "number", "Shelf padding", '', '', 'px');

        addOption("settings", "coverSize", "text", "Cover size", '', 'Percentage of shelf width', '%');
        addOption("settings", "coverMaxWidth", "number", "Cover max width", '', '', 'px');
        addOption("settings", "coverMarginH", "number", "Cover margin horizontal", '', '', 'px');
        addOption("settings", "coverMarginV", "number", "Cover margin vertical", '', '', 'px');
        addOption("settings", "coversAlign", "dropdown", "Cover align", ['right', 'left', 'center'], 'Horizontal align of covers');



        addOption("settings", "coverShadow", "text", "Cover shadow CSS");

        // addOption("settings", "viewMode", "dropdown", "Flipbook view mode", ['auto','webgl', '3d', '2d', 'swipe'], 'Override flipbook view mode');


        $('input.alpha-color-picker').alphaColorPicker()

        $('reset-defaults').click(function() {

            for (var key in defaults) {
                setOptionValue(key, defaults[key])
            }

        })

        function setOptionValue(optionName, value, type) {
            var type = type || 'input'
            var $elem = $(type + "[name='" + optionName + "']").attr('value', value).prop('checked', value);

            $("select[name='" + optionName + "']").val(value);
            // $("option[value='"+value+"']").attr("selected", true);

            return $elem
        }

        $('select[name="shelfImage"]').on('change', function() {
            // alert( this.value );
            var wood = options.PLUGIN_DIR_URL + '/img/shelf_wood.png'
            var glass = options.PLUGIN_DIR_URL + '/img/shelf_glass.png'
            var metal = options.PLUGIN_DIR_URL + '/img/shelf_metal.png'
            var $img = $('#shelf-image')
            var $input = $('input[name="image"]')
            switch (this.value) {
                case "wood":
                    $img.attr('src', wood)
                    $input.val(wood)
                    break;
                case "glass":
                    $img.attr('src', glass)
                    $input.val(glass)
                    break;
                case "metal":
                    $img.attr('src', metal)
                    $input.val(metal)
                    break;
                case "no shelf":
                    $img.attr('src', '')
                    $input.val('')
                    break;
                case "custom":
                    $img.attr('src', '')
                    $input.val('')
                    //$('.select-shelf-image').trigger('click')
                    selectShelfImage()
                    break;
            }
        })

        $('input[name="coverSize"]').on('change', function() {
            $('.book-shelf-preview-covers').find('.cover').css('width', $(this).val() + '%')
        })

        $('input[name="coverMaxWidth"]').on('change', function() {
            $('.book-shelf-preview-covers').find('.cover').css('max-width', $(this).val() + 'px')
        })

        $('input[name="coverMarginH"]').on('change', function() {
            $('.book-shelf-preview-covers').find('.cover').css('margin-left', $(this).val() + 'px')
            $('.book-shelf-preview-covers').find('.cover').css('margin-right', $(this).val() + 'px')
        })

        $('input[name="coverMarginV"]').on('change', function() {
            $('.book-shelf-preview-covers').find('.cover').css('margin-top', $(this).val() + 'px')
            $('.book-shelf-preview-covers').find('.cover').css('margin-bottom', $(this).val() + 'px')
        })

        $('input[name="coverShadow"]').on('change', function() {
            $('.book-shelf-preview-covers').find('.cover').css('boxShadow', $(this).val())
        })

        $('input[name="shelfImageMarginTop"]').on('change', function() {

            $('#shelf-image').css('margin-top', $(this).val() + 'px')
        })

        $('input[name="shelfBg"]').on('colorChange', function() {
            $('.book-shelf-preview').css('background-color', $(this).val())
        })

        $('input[name="shelfBg"]').on('change', function() {
            $('.book-shelf-preview').css('background-color', $(this).val())
        })

        $('input[name="shelfPadding"]').on('change', function() {

            $('.book-shelf-preview').css('padding', $(this).val() + 'px')
        })

        $('select[name="shelfAlign"]').on('change', function() {
            $('.book-shelf-preview').css('textAlign', String($(this).val()))
        })

        $('select[name="coversAlign"]').on('change', function() {
            $('.book-shelf-preview-covers').css('textAlign', String($(this).val()))
        })

        applyOptions()

        $('#shelf-options-form').submit(function(e) {

            e.preventDefault();

            var $form = $(this)

            var covers = $('.book-shelf-preview-covers').find('.cover')

            for (var i = 0; i < covers.length; i++) {
                var cover = covers[i]
                var id = cover.id
                var src = cover.firstChild.src

                var inputId = $('<input class="removeaftersave" type="hidden" name="covers[' + i + '][id]">').val(id).appendTo($form)

                //var inputSrc = $('<input class="removeaftersave" type="hidden" name="covers['+i+'][src]">').val(src).appendTo($form)

            }

            $form.find('.spinner').css('visibility', 'visible')

            $form.find('.save-button').prop('disabled', 'disabled').css('pointer-events', 'none')

            var data = $form.serialize() + '&action=r3d_shelf_save&shelfId=' + options.id

            $form.find('.removeaftersave').remove()

            $.ajax({
                type: "POST",
                url: $form.attr('action'), //.replace('admin-ajax','admin'),
                data: data,

                success: function(data, textStatus, jqXHR) {

                    $form.find('.spinner').css('visibility', 'hidden')
                    $form.find('.save-button').prop('disabled', '').css('pointer-events', 'auto')

                    console.log(data)

                },

                error: function(XMLHttpRequest, textStatus, errorThrown) {

                    alert("Status: " + textStatus);
                    alert("Error: " + errorThrown);

                }
            })

        })

        function unsaved() { // $('.unsaved').show()
        }

        function addOption(section, name, type, desc, values, help, inlineHelp) {

            var table = $("#shelf-" + section);
            var tableBody = table.find('tbody');
            var row = $('<tr valign="top"  class="field-row"></tr>').appendTo(tableBody);
            var th = $('<th scope="row">' + desc + '</th>').appendTo(row);
            var td = $('<td></td>').appendTo(row);
            var defaultValue = defaults[name] || ''

            switch (type) {
                case "text":
                    var input = $('<input type="text" name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        input.attr("value", stripslashes(options[name]));
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    // input.change(unsaved)
                    break;
                case "number":
                    var input = $('<input type="number" name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        input.attr("value", stripslashes(options[name]));
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    // input.change(unsaved)
                    break;
                case "color":
                    var input = $('<input type="text" class="alpha-color-picker" name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        input.attr("value", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    // input.wpColorPicker();
                    // input.change(unsaved)
                    break;
                case "textarea":
                    var elem = $('<textarea rows="10" cols="30" name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        elem.attr("value", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        elem.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        elem.attr('value', defaultValue);
                    }
                    // elem.change(unsaved)
                    break;
                case "checkbox":
                    var inputHidden = $('<input type="hidden" name="' + name + '" value="false"/>').appendTo(td);
                    var input = $('<input type="checkbox" name="' + name + '" value="true"/>').appendTo(td);
                    if (typeof(options[name]) != 'undefined') {
                        input.attr("checked", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        var val = options[name.split("[")[0]][name.split("[")[1].split("]")[0]]
                        input.attr("checked", val && val != 'false');

                    } else {
                        input.attr('checked', defaultValue);
                    }
                    // input.change(unsaved)
                    break;
                case "selectImage":
                    var input = $('<input type="hidden" name="' + name + '"/><img name="' + name + '"><a class="select-image-button button-secondary button80" href="#">Select image</a><a class="remove-image-button button-secondary button80" href="#">Remove image</a>').appendTo(td);
                    if (typeof(options[name]) != 'undefined') {
                        $(input[0]).attr("value", options[name]);
                        $(input[1]).attr("src", options[name]);
                    } else if (name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        $(input[0]).attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                        $(input[1]).attr("src", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        $(input[0]).attr('value', defaultValue);
                    }
                    // input.change(unsaved)
                    break;
                case "selectFile":
                    var input = $('<input type="text" name="' + name + '"/><a class="select-image-button button-secondary button80" href="#">Select file</a>').appendTo(td);
                    if (typeof(options[name]) != 'undefined') {
                        input.attr("value", options[name]);
                    } else if (name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    // input.change(unsaved)
                    break;

                case "dropdown":
                    var select = $('<select name="' + name + '"></select>').appendTo(td);
                    for (var i = 0; i < values.length; i++) {
                        var option = $('<option name="' + name + '" value="' + values[i] + '">' + values[i] + '</option>').appendTo(select);
                        if (typeof(options[name]) != 'undefined') {
                            if (options[name] == values[i]) {
                                option.attr('selected', 'true');
                            }
                        } else if (defaultValue == values[i]) {
                            option.attr('selected', 'true');
                        }
                    }
                    // select.change(unsaved)
                    break;

            }

            if (typeof inlineHelp != 'undefined')
                var span = $('<span class="description">' + inlineHelp + '</span>').appendTo(td)

            if (typeof help != 'undefined')
                var p = $('<p class="description">' + help + '</p>').appendTo(td)



        }

        // $(".tabs").tabs();
        $(".ui-sortable").sortable();

    });
})(jQuery);


function convertStrings(obj) {

    jQuery.each(obj, function(key, value) {
        // console.log(key + ": " + options[key]);
        if (typeof(value) == 'object' || typeof(value) == 'array') {
            convertStrings(value)
        } else if (!isNaN(value)) {
            if (obj[key] == "")
                delete obj[key]
            else
                obj[key] = Number(value)
        } else if (value == "true") {
            obj[key] = true
        } else if (value == "false") {
            obj[key] = false
        }
    });

}

function stripslashes(str) {
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +      fixed by: Mick@el
    // +   improved by: marrtins
    // +   bugfixed by: Onno Marsman
    // +   improved by: rezna
    // +   input by: Rick Waldron
    // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   input by: Brant Messenger (http://www.brantmessenger.com/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: stripslashes('Kevin\'s code');
    // *     returns 1: "Kevin's code"
    // *     example 2: stripslashes('Kevin\\\'s code');
    // *     returns 2: "Kevin\'s code"
    return (str + '').replace(/\\(.?)/g, function(s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}

function getParameterByName(name, url) {
    if (!url)
        url = window.location.href;
    url = url.toLowerCase();
    // This is just to avoid case sensitiveness  
    name = name.replace(/[\[\]]/g, "\\$&").toLowerCase();
    // This is just to avoid case sensitiveness for query parameter name
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results)
        return null;
    if (!results[2])
        return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}