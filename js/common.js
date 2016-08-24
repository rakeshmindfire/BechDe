function validate_form() {
    var msg = '';
    var password_patt = /^\w{6,}$/;
    var email_patt = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var email_error = document.getElementById('email_error');
    var password_error = document.getElementById('password_error');

    if (document.getElementById('email').value == '') {      
        msg += email_error.innerHTML = 'Enter email field.\n';
    
    } else if ( ! document.getElementById('email').value.match(email_patt)) {
        msg += email_error.innerHTML = "Not a valid email address.\n";
    }
 
    if (document.getElementById('email').value == '') {
       msg += password_error.innerHTML = "Enter password field.\n"; 
    
    } else if ( ! document.getElementById('pwd').value.match(password_patt)) {
       msg += password_error.innerHTML = "Password must be atleast 6 characters long.\n";
    }
    
    msg !== '' ? alert(msg) : 0  ;
    return msg === '' ? true: false;
}

$(document).ready(function() {
    $('#search_button').on('click',{ type: 1, start: 1, preserve_page: false }, fetch_products);
    $('#sorting-arrow-up').on('click', { type: 2, preserve_page: true }, fetch_products);
    $('#sorting-arrow-down').on('click', { type: 3, preserve_page: true }, fetch_products);
    
    // List all products
    $.ajax({
    url: "search.php?get_list=1",
    type: 'get',
    dataType: 'json',
    success: function(res) {
        var options = '';
        
        options += '<option value="0">All</option>';
        for (i=0 ; i<res.length ; i++) {
           options += '<option value=' + res[i].id + '>' + res[i].name + '</option>'; 
        }
        
        $('#search').append(options);
        $('#search_button').click();
    }
    });

});

var fetch_products = function(arg) {
    $('#product_list tbody').html('');
    $('#loader_image').removeClass('hide');
    
    var preserve = arg.data.preserve_page;
    
    if( ! preserve) {
        $('#product_pagination').addClass('hide').html('');
    }
    
    var table_body = '';
    
    var last_type = arg.data.type;
    
    if ( typeof arg.data.start !== 'undefined') {
        last_start = arg.data.start;
    }
    
    // Search for a category
    $.ajax({
        url: "search.php",
        type: 'post',
        dataType: 'json',
        data: { id : $('#search').val(),
                order_in :  arg.data.type,
                start_row : arg.data.start || last_start,
                no_of_rows : page_size 
              },
        success: function(res) {
            $('#no_data h2').removeClass('show').addClass('hide');
            $('#products_table').addClass('hide');
            $('#my_products').text('');
            
            if ( ! res.status) {
                $('#no_data h2').text('No products in this category').removeClass('hide').addClass('show');
            
            } else {
                $('#products_table').removeClass('hide');
                $('#my_products').text('My Products');
                var result = res.result;
                
                for ( i=0 ; i<result.length ; i++) {
                table_body += '<tr>\
                    <td>' + result[i].category_name +'</td>\
                    <td><img src="img/product/' + result[i].image + '" class="product-image"></td>\
                    <td>' + result[i].product_name + '</td>\
                    <td>' + result[i].amount + '</td>\
                    <td>' + result[i].description + '</td>\
                    <td>' + result[i].created_date + '</td>\
                    <td><a onclick=\'window.location="product_register.php?update_id=' + result[i].id + '";\'\
                           class="glyphicon glyphicon-pencil color-edit"></a>&nbsp;\
                        <a class="glyphicon glyphicon-remove color-remove remove-product-icon" onclick="show_modal('+ result[i].id +')" data-id=' + result[i].id +'></a>\
                    </td>\
                    </tr>';
                }

                $('#products_table tbody').html(table_body);
                $('#products_table').removeClass('hide');

                $('.product-image').on('click', show_image_modal);
                
                if ( ! preserve) {                    
                    var append_list = '<li class="active"><a>1</a></li>';
                    var no_of_pages = res.total / page_size;

                    if (no_of_pages > 1) {

                        for ( i=1 ; i < no_of_pages ; i++) {
                            append_list += '<li ><a>'+ (i+1) +'</a></li>';
                        }

                    $('#product_pagination').append(append_list).removeClass('hide');   
                    }
                }
                
                $('#product_pagination li ').off('click')
                    .on('click', 'a', function () {
                        $('#product_pagination li').removeClass('active');
                        $(this).closest('li').addClass('active');                       
                        var obj = {
                            data : { type: last_type, start:$(this).html() ,preserve_page:true }
                        };
                        fetch_products(obj);
                    });              
            }
            $('#loader_image').addClass('hide');
        }
    })
};

// Show delete confirmation modal
function show_modal(del_id)
{
    $('#myModalDelete').modal('show');
    
    $('#confirm_delete').off('click').on('click',function() {
        $.ajax({
            url: 'search.php',
            type: 'post',
            data: { delete_id:del_id },
            success: function() {
                $('#search_button').click(); 
                $('#confirm_message').text('Product deleted successfully!');
                $('#myModalDelete').modal('hide');
            }
        });
    });  
}

// Show image zoom modal
var show_image_modal = function() {
    var img_src = $(this).attr('src');  
    $('#zoomed_image').attr('src',img_src);
    $('#myModalImage').modal('show');
}
