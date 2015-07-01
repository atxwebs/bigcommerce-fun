<?php main();
/**
 * Turn on/off free shipping for ALL products in a store
 * @author rob mullins <mullinsr@live.com>
 * 'bigcommerce.php' is Copyright 2011 Bigcommerce - https://github.com/bigcommerce/bigcommerce-api-php/blob/master/LICENSE
 * This file is licensed to the public, provided "as-is".
 */

require 'bigcommerce.php';  //bigcommerce api library
use Bigcommerce\Api\Client as Bigcommerce; 
main();   

function main() {
  # configure your api credentials here:
  $r = connectToStore($credentials = array(
    'store_url' => 'https://www.*.com',
    'username'  => 'bigcommerce',
    'api_key'   => '*************'
  )); 
  !$r ? die("Could not connect to store\n") : null;
  # set mode for free shipping
  $mode = true; //true for on, false for off
  $processed = setFreeShipping($mode);
  # save products to log file, and quit
  fwrite($fp=fopen('free_shipping.log', 'a'), print_r($processed, true));
  fclose($fp);
  die("Program complete, please see 'free_shipping.log' for products affected\n");
}

/**
 * Connect to a given Bigcommerce store
 * @param mixed\array - the store's legacy api credentials
 * @return bool - true on successfully connecting to store, false on fail
 */
function connectToStore($crendentials) {
    Bigcommerce::configure($credentials);
    Bigcommerce::verifyPeer(false);
    Bigcommerce::setCipher('RC4-SHA');
    return Bigcommerce::getTime() ? true : false;
}

/**
 * Turn on/off free shipping for ALL products
 * @param bool - true to turn all on, false to turn all off
 * @return array - collection of all product IDs successfully processed 
 */
function setFreeShipping($mode) {
    $processed = array(); //Collection returns duplicates sometimes, hold the products processed
    $current_page = 1;
    $max_pages = getProductsPagesCount();
    while ($current_page <= $max_pages) {
        $filter = array(
            'page' => $current_page,
            'limit' => 250
        );
        $products = Bigcommerce::getProducts($filter); //get products according to page
        foreach ($products as $product) {
            $id = $product->id;
            if (!in_array($id, $processed)) {
                if (Bigcommerce::updateProduct($id, array('is_free_shipping' => $mode))) {
                  array_push($processed, $id); //save its id
                }
            }    
        }
        ++$current_page;
    }
    return $processed;
}

/**
 * Determine the total number of pages, @ 250 products per page
 * @return int - total number of pages
 */
function getProductsPagesCount() {
    $num_of_products = Bigcommerce::getProductsCount();
    if ($num_of_products <= 250) { //250 products in a page (note that 50 is actually the default)
        $max_pages = 1;
    } else {
        $pages_double = ($num_of_products / 250);
        $pages_int = ((int)($num_of_products / 250));
        if ($pages_double > $pages_int) { 
            $max_pages = $pages_int + 1;
        } else {
            $max_pages = $pages_int; //If 250 goes into number of products evenly (rare)
        }
    }
    return $max_pages;
}

?>
