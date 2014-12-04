<?php
require 'bigcommerce.php';
use Bigcommerce\Api\Client as Bigcommerce;
Bigcommerce::configure(array(
    'store_url' => 'https://www.*.com',
    'username'  => 'bigcommerce',
    'api_key'   => '02ce2b26456456540db4017d34'
));

Bigcommerce::verifyPeer(false);
Bigcommerce::setCipher('RC4-SHA');

$num_of_products = Bigcommerce::getProductsCount();

if ($num_of_products <= 250) {
    $max_pages = 1;
} else {
    $pages_double = ($num_of_products/250);
    $pages_int = ((int)($num_of_products/250));
    if ($pages_double > $pages_int) { 
        $max_pages = $pages_int + 1;
    } else {
        $max_pages = $pages_int; //If 250 goes into number of products evenly (rare)
    }
}

$processed = array(); //Collection returns duplicates sometimes, this will hold the ID of all products we processed. 
$file = './output.txt';
$fp = fopen($file, 'wb');

$current_page = 1;
while ($current_page <= $max_pages) {
    $filter = array(
    'page' => $current_page,
    'limit' => 250
);

    $products = Bigcommerce::getProducts($filter);
    foreach ($products as $product) {
            $id = $product->id;
            if (!in_array($id, $processed)){
                if($product->availability_description != NULL)
                    {
                        fwrite($fp, $product->name);
                        fwrite($fp, "\n----------\n");
                        break;
                    }
            array_push($processed, $id);
            }    
    }
    ++$current_page;
}
?>
