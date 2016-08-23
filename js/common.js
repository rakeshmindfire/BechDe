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
    $('#search_button').on('click', fetch_products);

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

var fetch_products = function() {
    $('#product_list tbody').html('');
    $('#loader_image').removeClass('hide');
    var table_body = '';
 
    // Search for a category
    $.ajax({
        url: "search.php",
        type: 'post',
        dataType: 'json',
        data: { id : $('#search').val() },
        success: function(res) {
            $('#no_data h2').removeClass('show').addClass('hide');
            $('#products_table').addClass('hide');
            $('#my_products').text(''); 
            console.log(res);

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

                $('.remove-product-icon').on('click', show_modal);
                $('.product-image').on('click', show_image_modal);
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
            url: "search.php",
            type: 'post',
            data: { delete_id:del_id },
            success: function(data) {
                console.log(data);        
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