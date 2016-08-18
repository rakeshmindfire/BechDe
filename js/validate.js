function validate_form() {
    var msg = '';
    var password_patt = /^\w{6,}$/;
    var email_patt = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var email_error = document.getElementById('email_error');
    var password_error = document.getElementById('password_error');

    if (document.getElementById('email').value == '') {      
        msg += email_error.innerHTML = 'enter email field\n';
    
    } else if ( ! document.getElementById('email').value.match(email_patt)) {
        msg += email_error.innerHTML = "not a valid email address\n";
    }
 
    if (document.getElementById('email').value == '') {
       msg += password_error.innerHTML = "enter password field\n"; 
    
    } else if ( ! document.getElementById('pwd').value.match(password_patt)) {
       msg += password_error.innerHTML = "password must be atleast 6 characters long \n";
    }
    
    msg !== '' ? alert(msg) : 0  ;
    return msg === '' ? true: false;
}