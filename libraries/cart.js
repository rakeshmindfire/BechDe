function itemsCart() {
    this.inflate_items = function () {    
        var cookie_array = document.cookie.split('; ');
        var items_i = -1;
        var retval = [];
        
        for( i=0; i<cookie_array.length; i++) {
         
            if (cookie_array[i].indexOf('cart') > -1) {
                items_i = i;
                break;
            }
        }
        // if cart name not present return empty array  ----- handle empty array as name not present , if empty array remove name for uniformity
        if ( items_i !== -1) { 
            var items_array = cookie_array[items_i].split('=')[1].split(',');
            if ( JSON.stringify(items_array) === '[""]') {    // if cart name in cookie present but no values in it then remove the name
                document.cookie = 'cart=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';

            } else {
                retval = items_array;
            }
        }
        return retval;
    }
    
    this.count_items = function() {
        return this.inflate_items().length;        
    }
    
    this.stitch = function(items_list) {
        document.cookie = 'cart=' + items_list.join();
    }
    
    this.remove_item = function(item) {
        var items_list = this.inflate_items();
        var item_index = items_list.indexOf(item);
        items_list.splice(item_index, 1);
        this.stitch(items_list);
    }
    
    this.add_item = function(item) {
        var items_list = this.inflate_items();
        items_list.push(item);
        this.stitch(items_list);
    }
        
};

var cart = new itemsCart();
