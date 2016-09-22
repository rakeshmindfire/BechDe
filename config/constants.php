<?php
/**
 * Constants declaration for QuickSeller.com
 *
 * @category   Config
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */
// Database Constants
define('SERVERNAME', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', 'mindfire');
define('DBNAME', 'QuickSeller');

// Image Constants
define('PROFILE_PIC', 'img/profile/');
define('PRODUCT_PIC', 'img/product/');
define('NOIMAGE', 'img/noimage.jpg');

// Log directory
define('LOG_DIR', 'log/');

// No of records per page
define('PER_PAGE_RECORD', 5);

// Activation mail sender's credentials 
define('SENDER_USERNAME', 'rakeshtill2017@gmail.com');
define('SENDER_PASSWORD', 'isinvssut');

// Encryption salt
define('SALT', 'quicksellersalt');

define('BASE_PATH','http://local.quickseller.com/');

// OAuth keys
define('OAUTH_TWITTER_CONSUMER_KEY', 'ngRy6rnBm1UmIxixNTFOXOMkl');
define('OAUTH_TWITTER_TOKEN', '777810478693163008-GmSVP0BR5TymdCKwHNp0MXASur9kx8z');
define('OAUTH_TWITTER_TOKEN_SECRET', 'SzJ5UroyrAm3hBpy4rJHQlQH8d9Zy7Ln9VmpMucClwYXo');
define('OAUTH_TWITTER_CONSUMER_SECRET', 'k0HXaBiIoMgmU3Lb3pfiLgy49m0gIsYyvpq01lzfPLveQ437dy');
?>