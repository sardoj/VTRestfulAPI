<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v.2.0.
 * If a copy of the MPL was not distributed with this file, 
 * you can obtain one at http://mozilla.org/MPL/2.0/.
 * 
 * The Original Code is VTRestfulAPI.
 * 
 * The Initial Developer of the Original Code is Jonathan SARDO.
 * Portions created by Jonathan SARDO are Copyright (C). All Rights Reserved.
 */

//Get products
require_once('RestfulApiCalls.php');

////////////////////////////////////////////////////////////////////
$crmUrl         = 'http://localhost/vtiger6.5';  //Your CRM URL
$userAccessKey  = 'TAnn8wHjEfbhx2Es'; //User access Key
$userLogin      = 'admin'; //User Login
$userPassword   = '123456'; //User Password
////////////////////////////////////////////////////////////////////


session_start();

$api = new RestfulApiCalls();

echo '<h1>Token</h1>';
if(empty($_SESSION["RestfulApiToken"]))
{
    //Signin by user access key...
    $token = $api->signInByKey($crmUrl, $userAccessKey);

    // ... or Signin by user login and password
    //$token = $api->signInByLoginPassword($crmUrl, $userLogin, $userPassword);

    if(!empty($token)) //Store token into session
    {
        $_SESSION["RestfulApiToken"] = $token;
    }

    pr($token);
}
else
{    
    $token = $_SESSION["RestfulApiToken"]; //Get token from session
    $api->setToken($crmUrl, $token);
    pr($token.' (from session)');
}

//Get products list (only actives products)
$a_products = $api->getEntities('Products', 'vtiger_products.discontinued:1');
echo '<h1>Products</h1>';
pr($a_products);

//Get product categories
$a_categories = $api->getPickListValues('Products', 'productcategory');
echo '<h1>Product categories</h1>';
pr($a_categories);

//Get product categories with dependencies
$a_categories = $api->getPickListValues('Products', 'productcategory', true);
echo '<h1>Product categories with dependencies</h1>';
pr($a_categories);

//Create a product
$product = array(
    "productname" => "Test",
    "unit_price" => "20.52"
);
$productId = $api->createEntity('Products', $product);
echo '<h1>New added product id</h1>';
pr($productId);

//Update product
$product_update = array(
    "productname" => "My product",
);
$productId = $api->updateEntity('Products', $productId, $product_update);
echo '<h1>Updated product id</h1>';
pr($productId);

//Get product by id
$o_product = $api->getEntityById('Products', $productId);

//Create a quote
$quantity = 3;
$vat_rate = 0.20; //20%
$quote = array(
    "subject" => "New Quote",
    "account_id" => $accountId,
    "contact_id" => "",
    "quote_date" => date("Y-m-d"),
    "validtill" => date("Y-m-d", strtotime("+30 days", mktime())),
    "quotestage" => "Created",
    "bill_street" => "100 street of beta test",
    "bill_code" => "34000",
    "bill_city" => "Montpellier",
    "bill_country" => "France",
    "ship_street" => "100 street of beta test",
    "ship_code" => "34000",
    "ship_city" => "Montpellier",
    "ship_country" => "France",
    "display_bank_name" => "1",
    "display_iban" => "1",
    "display_swift_code" => "1",
    "taxtype" => "group",
    "discount_percentage_final" => 0,
    "discount_amount_final" => 0,
    "shipping_handling_charge" => 0,
    "tax1_group_percentage" => 20,
    "tax2_group_percentage" => 0,
    "tax3_group_percentage" => 0,
    "shtax1_sh_percent" => 0,
    "pre_tax_total" => $o_product->unit_price * $quantity,
    "adjustment" => 0,
    "subtotal" => $o_product->unit_price * $quantity,
    "total" => $o_product->unit_price * $quantity * (1 + $vat_rate),
    "items" => json_encode(array(
        array(
            'hdnProductId' => $o_product->record_id,
            'qty' => $quantity,
            'listPrice' => $o_product->unit_price,
            'comment' => $o_product->description,
            'discount_amount' => 0,
            'discount_percentage' => 0
        )
    ))
);

$quoteId = $api->createEntity('Quotes', $quote);
echo '<h1>New added quote id</h1>';
pr($quoteId);

//Delete quote
$result = $api->deleteEntity('Quotes', $quoteId);
echo '<h1>Delete quote</h1>';
pr($result);



function pr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}