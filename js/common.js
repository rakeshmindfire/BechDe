/**
 * Javascript for QuickSeller.com
 *
 * @category   Javascript
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */

/**
 * To validate fields in login form
 *
 * @access public
 * @param void
 * @return boolean 
 */
function validate_form() {
    var return_value = true;
    var msg = '';
    var password_patt = /^\w{6,}$/;
    var email_patt = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var email_error = document.getElementById('email_error');
    var password_error = document.getElementById('password_error');

    if (document.getElementById('email').value === '') {      
        msg += email_error.innerHTML = 'Enter email field.\n';
    
    } else if ( ! document.getElementById('email').value.match(email_patt)) {
        msg += email_error.innerHTML = "Not a valid email address.\n";
    }
 
    if (document.getElementById('pwd').value === '') {
       msg += password_error.innerHTML = "Enter password field.\n"; 
    
    } else if ( ! document.getElementById('pwd').value.match(password_patt)) {
       msg += password_error.innerHTML = "Password must be atleast 6 characters long.\n";
    }

    if(msg !== '') {
        alert(msg);
        return_value = false;
    }
    
    return return_value;
}


var last_start = 1;     // Store last page number
var last_status = 1;    // Store last fetch filtered by status (active or inactive)
var last_type = 1;      // Store last fetch filtered by category
var search_button = $('#search_button');


/**
 * To show confirmation modal on clicking of delete option
 *
 * @access public
 * @param integer del_id Id of the product to be deleted
 * @return void 
 */
function show_modal(del_id) { 
    $('#myModalDelete').modal('show');    
    $('#confirm_delete').off('click').on('click',function() {
        $.ajax({
            url: 'search.php',
            type: 'post',
            data: { delete_id:del_id },
            success: function() {
                search_button.click(); 
                $('#confirm_message').text('Product deleted successfully!');
                $('#myModalDelete').modal('hide');
            }
        });
    });  
}

/**
 * To show modal of the image when clicked
 *
 * @access public
 * @param void
 * @return void 
 */
function show_image_modal() {
    var img_src = $(this).attr('src'); 
    $('#zoomed_image').attr('src',img_src);
    $('#my_modal_image').modal('show');
}

/**
 * To change the status of the product 
 *
 * @access public
 * @param integer id Id of the product whose status is to be toggled
 * @return void 
 */
function change_status(id) { 
    $.ajax({
        url: 'search.php',
        type: 'post',
        data: { change_id: id, status: last_status },
        success: function() {
            var obj = { data : {} };
            fetch_products(obj);
        }
    });
}

/**
 * To show message in header
 *
 * @access public
 * @param string message Message to show in the header
 * @return void 
 */
function show_h2_message(message) {
      $('#no_data h2').text(message).removeClass('hide').addClass('show');
} 

/**
 * To fetch all products based on provided filter
 *
 * @access public
 * @param object arg Contains parameters for filtering. 
 *       members: {
 *                   integer data.type Type of filtering. 1 for categories,
 *                       2 for ascending sort, 3 for descending sort
 *                   integer data.start Page number to fetch
 *                   boolean data.preserve_page Whether to save the current page number
 *                }
 * @return void 
 */
function fetch_products(arg) {
    var preserve = arg.data.preserve_page;
    var table_body = '';       
    var product_pagination = $('#product_pagination');
    var products_table = $('#products_table');
    
    $('#product_list tbody').html('');
    $('#loader_image').removeClass('hide');
    $('#search_category,#status_tab').removeClass('hide');

    // Empty Pagination container on filtering categories
    if( ! preserve) {
        product_pagination.addClass('hide').html('');
    }

    // Store the status during sorting and filtering
    if ( typeof arg.data.status !== 'undefined') {
        last_status = arg.data.status;
    }
    
    // Store the category during sorting, pagination and status change
    if ( typeof arg.data.type !== 'undefined') {
        last_type = arg.data.type;
    }

    // Store the page number during sorting
    if ( typeof arg.data.start !== 'undefined') {
        last_start = arg.data.start;
    }

    var status_arg = arg.data.status || last_status;
    
    // Search for a category
    $.ajax({
        url: 'search.php',
        type: 'post',
        dataType: 'json',
        data: { id : $('#search').val(),
                order_in :  arg.data.type || last_type,
                status: status_arg,
                start_row : arg.data.start || last_start,
                no_of_rows : page_size 
              },
        success: function(res) {
            $('#no_data h2').removeClass('show').addClass('hide');
            products_table.addClass('hide');
            $('#my_products').text('');
            
            if ( ! res.products_exist) {
              $('#search_category,#status_tab').addClass('hide');
                show_h2_message('No products in this account');
            }
            
            else if ( ! res.status) {
                show_h2_message('No products in this category');
            
            } else {
                products_table.removeClass('hide');
                $('#my_products').text('My Products');
                var result = res.result;
                
                for (var i = 0 ; i < result.length ; i++) {
                table_body += '<tr>' 
                    + '<td>' + result[i].category_name +'</td>' 
                    + '<td><img class="product-image" src="img/ajax-loader.gif" class="product-image" data-image='+ result[i].image +'></td>' 
                    + '<td>' + result[i].product_name + '</td>' 
                    + '<td>' + result[i].amount + '</td>' 
                    + '<td>' + result[i].description + '</td>' 
                    + ((user_role === 1) ? ('<td>' + result[i].name + '</td>') : '' )
                    + '<td>' + result[i].created_date + '</td>' 
                    + '<td>';
            
                    if (status_arg === 3) {
                        table_body += 'SOLD <br> '
                            +'<a type="button" class="btn btn-info" onclick="show_buyer_profile('+ result[i].id +')"' 
                            + '>Buyer Info</a>';                   
                    } else {                  
                        table_body += '<a onclick=\'window.location="product_register.php?update_id=' + result[i].id + '";\'' 
                        + 'class="glyphicon glyphicon-pencil color-edit modify-icons" data-toggle="tooltip" data-placement="top" title="Edit Item"></a>&nbsp;' 
                        + '<a class="glyphicon glyphicon-remove color-remove modify-icons" onclick="show_modal('+ result[i].id +')" data-id=' + result[i].id 
                        +'data-toggle="tooltip" data-placement="top" title="Delete Item"></a>' 
                        + '<a class="glyphicon modify-icons '+ (status_arg === 1 ? 'glyphicon-minus' : 'glyphicon-ok') +'"' 
                        + 'onclick="change_status('+ result[i].id +')" data-toggle="tooltip" data-placement="top" title="'
                        + (status_arg === 1 ? 'Deactivate' : 'Activate') +' Item "></a>' ;
                    }
            
                    table_body += '</td></tr>';
                }
                
                products_table.find('tbody').html(table_body);
                show_product_images();
                products_table.removeClass('hide');
                $('.product-image').on('click', show_image_modal);
                                             
                // Modfiy pagination container on filtering category and page entry
                if ( ! preserve) {                    
                    var append_list = '<li class="active"><a>1</a></li>';
                    var no_of_pages = res.total / page_size;

                    if (no_of_pages > 1) {

                        for (var i = 1 ; i < no_of_pages ; i++) {
                            append_list += '<li ><a>'+ (i+1) +'</a></li>';
                        }

                    product_pagination.append(append_list).removeClass('hide');   
                    }
                }
               
                product_pagination.find('li').off('click')
                    .on('click', 'a', function () {
                        product_pagination.find('li').removeClass('active');
                        $(this).closest('li').addClass('active');                       
                        var obj = {
                            data : { start:$(this).html() ,preserve_page:true }
                        };
                        fetch_products(obj);
                    });              
            }
            
            $('#loader_image').addClass('hide');
        }
    });
}

/**
 * To show profile of user
 *
 * @access public
 * @param object arg {
 *          integer data.get_buyer id of the buyer (if undefined then show seller profile whose id is in data-seller)
 *          }
 * @return void
 */
function show_user_profile (arg) {
        var user_id;
        
        // arg.data not set when called by a seller
        if ( typeof arg.data === 'undefined') {
            user_id = $(this).data('seller');
        
        } else { // arg.data.get_buyer set with user_id when called by a buyer
            user_id = arg.data.get_buyer;
        }
         
        $.ajax({
            url: 'search.php',
            type: 'post',
            dataType: 'json',
            data: { get_user : user_id },
            success: function(res) {
               
                // Populate modal with appropriate data
                var seller_data = res.result;
                var seller_address = seller_data.ofc_addrstreet + ', ' 
                    + seller_data.ofc_addrcity + ', ' + seller_data.ofc_addrstate_name
                    + ', ' +  seller_data.ofc_addrzip ;
        
                $('#seller_image').attr('src',seller_data.image === null ? no_image : 'img/profile/' + seller_data.image);
                $('#seller_name').text(seller_data.user_name);
                $('#seller_sex').text(seller_data.gender === 'M' ? 'Male': 'Female');
                $('#seller_dob').text(seller_data.dob);
                $('#seller_mob').text(seller_data.contact_num);
                $('#seller_bio').text(seller_data.comment);
                $('#seller_prefcomm').text(seller_data.preferred_comm);
                $('#seller_addr').text(seller_address.indexOf('null')>=0 ? 'N/A': seller_address);
                $('#seller_email').text(seller_data.email);
                $('#seller_info_modal').modal('show');    
            }
        });    
}

/**
 * To show profile of buyer
 *
 * @access public
 * @param integer product_id the id of the product whose buyer info required
 * @return void
 */
function show_buyer_profile(product_id) {
      $.ajax({
            url: 'search.php',
            type: 'post',
            dataType: 'json',
            data: { get_buyer_of : product_id },
            success: function(res) {
                var obj = {
                            data : {get_buyer :res.result},
                        };
                show_user_profile(obj);
            }
        });
}

/**
 * Update count of items in checkout button
 *
 * @access public
 * @param void
 * @return void 
 */
function update_cart_count() {
    if (cart.count_items() > 0) {
        $('#items_in_cart').text(cart.count_items());
        $('#checkout_button').removeClass('hide');
    } else {
        $('#checkout_button').addClass('hide');
    }
}

/**
 * Actions for product_list.php
 *
 * @access public
 * @return void
 */
function product_list_bind() {
    
        search_button.on('click',{ type: 1, start: 1, preserve_page: false }, fetch_products);
        $('#sorting-arrow-up').on('click', { type: 2, preserve_page: true }, fetch_products);
        $('#sorting-arrow-down').on('click', { type: 3, preserve_page: true }, fetch_products);
        $('#status_tab li ').off('click')
            .on('click', 'a', function () {
                $('#status_tab li').removeClass('active');
                $(this).closest('li').addClass('active');                       
                var obj = {
                    data : { status:$(this).data('value') ,start: 1, preserve_page:false }
                };
                fetch_products(obj);
            }); 

        var cur_page = window.location.href;
        $(':reset').on('click',function() {
             window.location = cur_page;
        }); 
        
        $.ajax({
        url: 'search.php?get_list=1',
        type: 'get',
        dataType: 'json',
        success: function(res) {
            var options = '';

            options += '<option value="0">All</option>';
            for (var i = 0; i < res.length; i++) {
               options += '<option value=' + res[i].id + '>' + res[i].name + '</option>'; 
            }

            $('#search').append(options);
            search_button.click();
        }
        });
}

/**
 * Actions for permissions.php
 *
 * @access public
 * @return void
 */
function permissions_bind() {
      
        $.ajax({
        url: 'modify_permissions.php',
        type: 'post',
        dataType: 'json',
        data: { get_permissions : 1},
        success: function(res) {

            // Create roles tab
            var roles_list_tab = '';

            for(var i = 2; i <= res.role.length; i++) {
                roles_list_tab += '<li class="capitalize ' + (i===2 ? 'active' : '') + '" data-id='+ res.role[i-1].id +
                    '><a href="#">' + res.role[i-1].name + '</a></li>';
            }  

            var roles_tab = $('#role_tab');
            roles_tab.append(roles_list_tab);

            // Bind tabs to show the corresponding table
            roles_tab.find('li').off('click').on('click', function () {
                roles_tab.find('li').removeClass('active');
                $(this).closest('li').addClass('active');
                $('table').addClass('hide');
                $('#table_'+($(this).data('id'))).removeClass('hide');   
            });

            // Create separate tables for roles
            var permissions_table;

            for (var role_i = 2; role_i <= res.role.length; role_i++) {

                //  Create table template
                permissions_table = '<table class="table table-bordered table-condensed '+ (role_i!==2 ?'hide' : '') +
                    '" id="table_'+ res.role[role_i-1].id +'" ><thead><tr><th>Resources</th></tr></thead><tbody></tbody></table>';
                 $('#permissions_div').append(permissions_table);

               // Create table headers
                var permissions_headers = '';

                for (var permission_i=1; permission_i<=res.permission.length; permission_i++) {
                    permissions_headers += '<th class="capitalize">' + res.permission[permission_i-1].name + '</th>';
                }

                $('#table_' + res.role[role_i-1].id + ' tr').append(permissions_headers); 

                // Create checkboxes in the table
                var permissions_checkbox;

                for (var resource_i = 1; resource_i <= res.resource.length; resource_i++) {
                    permissions_checkbox = '<tr><td><b class="capitalize">' + res.resource[resource_i-1].name + '</b></td>';

                    for (var permission_i = 1; permission_i <= res.permission.length; permission_i++) {
                        var id = role_i + '-' + resource_i + '-' + permission_i;
                        permissions_checkbox += '<td><input type ="checkbox" id="'+ id 
                            + '" '+ ($.inArray(id,res.present_permissions_imploded)>=0 ? 'checked' : '')+'></td>';
                    }

                    permissions_checkbox += '</tr>';
                    $('#table_' + res.role[role_i-1].id + ' tbody').append(permissions_checkbox);
                }
            }

            // Post data on Submit button click
            $('#submit_permissions').off('click').on('click', function() {
                var data = [];
                $( "input:checked" ).each( function() {
                    data.push($(this).attr('id'));
                });
                $.ajax({
                    url: 'modify_permissions.php',
                    type: 'post',
                    dataType: 'json',
                    data: { save_permissions : 1, permissions_data : data },
                    success: function(res) {
                            if (res.status) {
                                $("#saved").removeClass('hide');
                                setTimeout(function() { $("#saved").addClass('hide'); }, 2000);
                            }
                    }
                });            
            });

            // Reload data from database on reset click
            $('#reset_permissions').off('click').on('click', function() {
                location.reload();
            });
        }
        });  
}

/**
 * Actions for product_deals.php
 *
 * @access public
 * @return void
 */
function product_deals_bind() {
    $.ajax({
        url: 'search.php',
        type: 'post',
        dataType: 'json',
        crossDomain: true,
        data: { id : 0,
                order_in : last_type,
                status: 1,
                start_row : 1,
                no_of_rows : 200
              },
        success: function(res) {
            display_data(res.result, false);
            
            // Show number of items in the cart
            update_cart_count();
            // Buy button Action
            $('.buy_button').on('click', function() {
                // Add to cart in cookies 
                cart.add_item($(this).data('id'));
                // Change text to 'added to cart'
                $(this).text('Added').css('pointer-events', 'none').
                        removeClass('btn-success').addClass('btn-warning'); 
                // Update count if add button clicked        
                update_cart_count();
            });           
        }
        });
}

/**
 * Actions for purchase.php
 *
 * @access public
 * @return void
 */
function history_bind() {
        $.ajax({
        url: 'search.php',
        type: 'post',
        dataType: 'json',
        data: { is_history: 1 },
        success: function(res) {
                display_data(res.result, true);
        }
        });
}

/**
 * Actions for purchase.php
 *
 * @access public
 * @return void
 */
function purchase_bind() {        
        // If no items in cart
        if(cart.count_items() === 0) {
            var no_items_msg = '<h3>No Items in the cart</h3><h4>Click <a href="product_deals.php">here</a> to add products in cart</h4>';
            $('#items_to_be_purchased').append(no_items_msg);
            $('#billing_info').addClass('hide');        
        } else {    
        // Show details of items in the cart
        var product_id = cart.inflate_items();
        var download_image = [];

        for (var i=0; i < product_id.length; i++) {       
            $.ajax({
            url: 'search.php',
            type: 'post',
            dataType: 'json',
            data: { get_product: product_id[i] },
            success: function(res) {
                var product = res.result;
                var delivery_date = new Date();
                delivery_date.setTime(delivery_date.getTime() + (7 * 24 * 60 * 60 * 1000));    // Add 7 days to current date
                var item_div = '<div class="panel-group col-sm-9">'
                    + '<div class="panel panel-default">'
                    + '<div class="panel-heading">'
                    + '<h4 class="panel-title">'
                    + '<a data-toggle="collapse" href="#collapse'+ product.id +'">'+ product.product_name +'</a>'
                    + '</h4>'
                    + '</div>'
                    + '<div id="collapse'+ product.id +'" class="panel-collapse collapse in">'
                    + '<div class="panel-body">'
                    +  '<img id="item_image'+ product.id +'" class="col-sm-2 img-rounded panel-images" src="img/ajax-loader.gif">'
                    + '<div id="product_info" class="col-sm-9">'
                    + '<div><b>Category : </b>'+ product.category_name +'</div>'
                    + '<div><b>Description : </b>'+ product.description +'</div>'
                    + '<div><b>Price : </b><span class="item_price">'+ product.amount +'</span></div>'
                    + '<div><b>Delivered by : </b>'+ delivery_date.toDateString() +'</div>'
                    + '<div><b>Seller :</b><a class="btn btn-link seller_info" data-seller='+ product.seller_id +'>'+ product.seller_name +'</a>'
                    + '</div>'
                    + '<button class="btn btn-danger glyphicon glyphicon glyphicon-remove pull-right remove_item_from_cart"'
                    + ' data-id='+ product.id +'>Remove</button>'
                    + '</div>'
                    + '</div>'
                    + '</div>'
                    + '</div>';

                // Append product details 
                $('#items_to_be_purchased').append(item_div);
                
                // Bind seller profile modal
                $('.seller_info').off('click').on('click', show_user_profile);               
                
                // Remove data from cart on item remove option
                $('.remove_item_from_cart').off('click').on('click', function() {
                    cart.remove_item(String($(this).data('id')));
                    location.reload();
                });
            
                // Replace loader image with product image after image load
                download_image[i] = new Image();
                download_image[i].src = 'img/product/' + product.image;
                download_image[i].onload = function() {
                  $('#item_image' + product.id).attr('src', this.src);
                };
                download_image[i].onerror = function() {
                  $('#item_image' + product.id).attr('src', 'img/noimage.jpg');
                };
                
                // Show bill amount
                var total_bill = 0;        
                $('.item_price').each(function() { total_bill = total_bill + Number($(this).text())});
                $('#bill').text(String(total_bill.toFixed(2)));
                
                }
            });
        }
       
        // Show purchase button and bind confirmation modal
        $('#confirm_purchase_button').removeClass('hide').on('click',function() {
           $('#confirm_purchase_modal').modal('show');  
        });
        // Show bill
        $('#billing_info').removeClass('hide');
        // Perform db operations on confirm purchase 
        $('#confirm_purchase').off('click').on('click',function() {
           $('body').css({'pointer-events':'none', 'opacity':'0.2'});
           $('#processing').removeClass('hide').css('opacity','1');
            $.ajax({
                url: 'search.php',
                type: 'post',
                dataType: 'json',
                data: { purchase_id : JSON.stringify(cart.inflate_items()) },
                success: function(res) {
                    if (res.status) {
                        // Clear Cart and Redirect to deals page
                        cart.clear();                               
                        window.location = 'payment_success.php';    
                    } else {
                        alert ('Error occured during purchase');
                    }
                }
            });
        });   
    }
}

/**
 * Display data in datatables
 *
 * @access public
 * @param JSON response containing values to populate the datatable
 * @param boolean is_history_page whether the page is history.php( this page requires a different view)
 * @return void
 */
function display_data(response, is_history_page) {
    var total_rows = response.length;
    var aoColdef = [ 
            null,
            null,
            { "bSortable": false },
            null,
            null,
            null,
            { "bSortable":  false },
        ];  
        
    var data_table = $('#deals').dataTable({
        "sPaginationType":"full_numbers",
        "bRetrieve": true,
        "aoColumns": aoColdef,
        "iDisplayLength": 5,
        "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
        "sDom": '<"top"if>rt<"bottom"lp><"clear">'
       
    });
    
    data_table.fnClearTable();

    var img_modal, action_buttons;
    if (0 < total_rows) {

        for (var i = 0; i < total_rows; i++) {

            img_modal = '<img data-image='+ response[i].image +' src="img/ajax-loader.gif" class="product-image">' ;
            action_buttons = '<a type="button" class="btn btn-info seller_details" data-seller="' + response[i].seller_id +' ">Seller Info</a><br><br>';
            // If history page then show the option to view buyer info else seller info
            action_buttons += ! is_history_page ? '<a type="button" class="btn glyphicon glyphicon-shopping-cart buy_button'
                + ((cart.inflate_items().indexOf(response[i].id)) !== -1 ?' btn-warning" style="pointer-events:none">Added'
                :' btn-success" data-id=' + response[i].id + '>Buy') + '</a>' : '';
            var date = new Date(is_history_page ? response[i].purchase_date : response[i].created_date);
            var row = [response[i].product_name,
                response[i].category_name,
                img_modal,
                response[i].amount,
                response[i].description,
                date.toDateString(),
                action_buttons
            ];

            data_table.fnAddData(row, false);            
        }     
    data_table.fnDraw();
    }

    // Bind several functions on first draw
    bind_table_functions();

    // Bind same functions on redraw
    $('#deals').on( 'draw.dt', function () {
           bind_table_functions();
    });
}

/**
 * Show product images after loading
 *
 * @access public
 * @return void
 */
function show_product_images() {
    // Replace loader image with product image after image load
    $('.product-image').each ( function() {
        var img = $(this);
        var download_image = new Image(); 
        download_image.src = 'img/product/' + $(this).data('image');
        download_image.onload = function() {
            img.attr('src', this.src); 
        };
        download_image.onerror = function() {
            img.attr('src', 'img/noimage.jpg');
        };
    });
}

/**
 * Bind modal and image functionalities in datatables
 *
 * @access public
 * @return void
 */
 function bind_table_functions() {
    // Show modals on clicking image and seller info button
    $('.product-image').off('click').on('click', show_image_modal);
    $('.seller_details').off('click').on('click', show_user_profile);   
    show_product_images();
}

/**
 * jQuery function on document ready
 *
 * @access public
 * @param void
 * @return void 
 */
$(document).ready(function() {
    
    switch(location.pathname.substring(1)) {
        case "product_list.php":       
            product_list_bind();
            break;
       
        case "permissions.php": 
            permissions_bind();
            break;
            
        case "product_deals.php":
            product_deals_bind();
            break;
        
        case "history.php":
            history_bind();
            break;
         
        case "purchase.php":
            purchase_bind();
            break;
        
        default:
            break;
    }
})
