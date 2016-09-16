/**
 * Library to handle cart items
 *
 * @category   Library
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */

var cart = {
    /**
     * Store all items in an array
     *
     * @access public
     * @return array
     */
    inflate_items : function () {    
        var cookie_array = document.cookie.split('; ');
        var items_i = -1;
        var retval = [];
        
        // Find index of the array containing the items
        for ( var i=0; i<cookie_array.length; i++) {
         
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
    },
    
    /**
     * Count number of items in the cart
     *
     * @access public
     * @return integer
     */
    count_items : function() {
        return this.inflate_items().length;        
    },
    
    /**
     * Convert array of items to cookie format
     *
     * @access public
     * @param array items_list list of items
     * @return array
     */
    stitch : function(items_list) {
        document.cookie = 'cart=' + items_list.join();
    },
    
    /**
     * Remove an item from cart
     *
     * @access public
     * @param integer item Id of item to be removed
     * @return void
     */ 
    remove_item : function(item) {
        // Extract existing items and find index of item to be removed
        // Remove item with help of its index and convert list back to format
        var items_list = this.inflate_items();
        var item_index = items_list.indexOf(item);
        items_list.splice(item_index, 1);
        this.stitch(items_list);
    },
    
    /**
     * Add an item in the cart
     *
     * @access public
     * @param integer item Id of item to include in the cart
     * @return void
     */ 
    add_item : function(item) {
        // Extract existing items and push new item to it
        // Then convert list back to cookie format
        var items_list = this.inflate_items();
        items_list.push(item);
        this.stitch(items_list);
    },

    /**
     * Clear all items from the cart
     *
     * @access public
     * @return void
     */ 
    clear : function() {
         document.cookie = "cart=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }       
};
