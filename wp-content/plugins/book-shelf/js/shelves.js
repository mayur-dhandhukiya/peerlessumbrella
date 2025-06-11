/*
author http://codecanyon.net/user/creativeinteractivemedia
*/

var R3D = R3D || {};

(function($) {

    function downloadObjectAsJson(exportObj, exportName){
        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(exportObj);
        var downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href",     dataStr);
        downloadAnchorNode.setAttribute("download", exportName + ".json");
        document.body.appendChild(downloadAnchorNode); // required for firefox
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
      }


    if(typeof shelves_json != "undefined"){
        downloadObjectAsJson(shelves_json, "shelves")
    }

    R3D.EditShelves = function() {

        this.shelves = $.parseJSON(shelves);
        var arr = []
        for(var key in this.shelves){
                arr.push(this.shelves[key])
        }
        this.shelves = arr
        // console.log(this.books);
        var self = this

        function addShelf(shelf) {
            $('<tr>' + '<th scope="row" class="manage-column column-cb check-column">' + '<input type="checkbox" class="row-checkbox" name="' + shelf.id + '">' + '</th>      ' + '<td>' + '<strong><a href="#" class="edit" title="Edit" name="' + shelf.id + '">' + shelf.name + '</a></strong>' + '<div class="row-actions"><span name="' + shelf.id + '" class="edit"><a href="#" title="Edit this item">Edit</a> | </span><span class="inline hide-if-no-js duplicate" name="' + shelf.id + '"><a href="#" title="Duplicate shelf" >Duplicate</a> | </span><span class="trash" name="' + shelf.id + '" ><a href="#" title="Move to trash" >Trash</a></span>' + '</div>' + '</td>' + '<td>[bookshelf id="' + shelf.id + '"]   <div id="'+shelf.id+'" class="button-secondary copy-shortcode">Copy</div>        </td>' + '<td>' + shelf.date + '</td>' + ' </tr>').appendTo($('#shelves-table'))
        }

        var keys = []
        for (var key in this.shelves) {
            keys.push(key);
            if(typeof this.shelves[key].date == 'undefined')
                this.shelves[key].date = '';
        }

        function dynamicSort(property) {
            var sortOrder = 1;
            if(property[0] === "-") {
                sortOrder = -1;
                property = property.substr(1);
            }
            return function (a,b) {
                var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
                return result * sortOrder;
            }
        }
        this.shelves.sort(dynamicSort("date"))
        this.shelves.reverse()

        var pageSize = 20
        var currentPage = 0
        var totalPages = parseInt(keys.length / pageSize) + 1
            //debugger
        if (keys.length < pageSize)
            $('.tablenav-pages').addClass('one-page')
        $('.total-pages').text(totalPages)
        $('.displaying-num').text(keys.length + ' items')

        function showPage(index) {
            $('#shelves-table').empty()
            for (var i = pageSize * index; i < pageSize * (index + 1); i++) {
                var shelf = self.shelves[i]
                if (shelf)
                    addShelf(shelf)
            }
            $('.current-page').val(index + 1)

            $('.edit').click(function(e) {
                e.preventDefault()
                var id = this.getAttribute("name")
                window.location = window.location.origin + window.location.pathname + '?page=book_shelves&action=edit&shelfId=' + id
            })
            $('.duplicate').click(function(e) {
                e.preventDefault()
                var id = this.getAttribute("name")
                window.location = window.location.origin + window.location.pathname + '?page=book_shelves&action=duplicate&shelfId=' + id
            })
            $('.trash').click(function(e) {
                e.preventDefault()
                var id = this.getAttribute("name")
                window.location = window.location.origin + window.location.pathname + '?page=book_shelves&action=delete&shelfId=' + id
            })
            $('.undo').click(function(e) {
                e.preventDefault()
                window.location = window.location.origin + window.location.pathname + '?page=book_shelves&action=undo'
            })
        }

        showPage(currentPage)

        $('.first-page').click(function() {
            currentPage = 0
            showPage(currentPage)
        })
        $('.prev-page').click(function() {
            if (currentPage > 0) currentPage--;
            showPage(currentPage)
        })
        $('.next-page').click(function() {
            if (currentPage < (totalPages - 1)) currentPage++;
            showPage(currentPage)
        })
        $('.last-page').click(function() {
            currentPage = totalPages - 1
            showPage(currentPage)
        })

        $('.bulkactions-apply').click(function() {
            var action = $(this).parent().find('select').val()
            if (action != '-1') {
                // console.log(action)
                var list = []
                $('.row-checkbox').each(function() {
                    // console.log(this)
                    if ($(this).is(':checked'))
                        list.push($(this).attr('name'))
                        // console.log(list)
                })
                if (list.length > 0) {
                    window.location = window.location.origin + window.location.pathname + '?page=book_shelves&action=delete&shelfId=' + list.join(",")

                    /*$.get(window.location.origin + window.location.pathname, { action: "delete", bookId: list.join(",") })
                      .done(function( data ) {
                    	alert( "Data Loaded: " + data );
                      });*/


                }
            }
        })

        $('.copy-shortcode').click(function(){
            var id = $(this).attr("id")
            var shortcode = "[bookshelf id='"+id+"']"
            var copyText = document.getElementById("copy-text-hidden");
            copyText.value = shortcode

              /* Select the text field */
            copyText.select();

              /* Copy the text inside the text field */
              document.execCommand("copy");
        })

        $('.import').click(function(e) {
                e.preventDefault()
                importShelves()
            })


        function importShelves(){

            var json = $('#flipbook-admin-json').val()

            json = JSON.stringify(JSON.parse(json))

            if (confirm('Import shelves from JSON. This will delete any existing shelves. Are you sure?')) {

                $.ajax({

                    type: "POST",
                    url: 'admin-ajax.php?page=book_shelves',
                    data: {
                        shelves: json,
                        security: window.r3d_nonce,
                        action: "r3d_import"
                    },

                    success: function(data, textStatus, jqXHR) {

                        location.href = location.origin + location.pathname + "?page=book_shelves"

                    },

                    error: function(XMLHttpRequest, textStatus, errorThrown) {

                        alert("Status: " + textStatus);
                        alert("Error: " + errorThrown);

                    }
                })

            }

        }


    }

    $(document).ready(function() {
        new R3D.EditShelves()
    });
})(jQuery);