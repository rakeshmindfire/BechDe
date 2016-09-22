<?php
/**
 * Library for encryption-decryption of activation mail parameter 
 *
 * @category   Library
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */

/**
 * To encrypt a text
 *
 * @access public
 * @param string $text Input text for encryption
 * @return string
 */
function simple_encrypt($text)
{  
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}

/**
 * To decrypt an encrypted text
 *
 * @access public
 * @param string $text Encrypted text
 * @return string
 */
function simple_decrypt($text)
{  
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB,
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}
