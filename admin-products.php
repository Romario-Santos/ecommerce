<?php
use \Hcode\PageAdmin;
use \Hcode\Model\Products;
use \Hcode\Model\user;

$app->get("/admin/products",function(){
User::verifyLogin();
    $products = Products::listAll();
    $pageAdmin = new PageAdmin();

    $pageAdmin->setTpl("products",array("products"=>$products));


});

$app->get("/admin/products/:idproducts/delete",function($idproducts){

       
    User::verifylogin();

    $producty = new Products();

    $producty->get((int)$idproducts);

    $producty->delete();

    header("Location: /admin/products");
    exit;


});


$app->get("/admin/products/create",function(){
    User::verifyLogin();
    
    $pageAdmin = new PageAdmin();

    $pageAdmin->setTpl("products-create");


});

$app->post("/admin/products/create",function()
{
    User::verifyLogin();
    
var_dump($_POST);
    
  $products = new Products();

  $products->setData($_POST);

  $products->save();

  header("Location: /admin/products");
  exit;
  


});


$app->get("/admin/products/:idproduct",function($idproduct){
    User::verifyLogin();
    $product = new Products();
    $product->get((int)$idproduct);

    $pageAdmin = new PageAdmin();

    $pageAdmin->setTpl("products-update",['product'=>$product->getValues()]);


});

$app->post("/admin/products/:idproducty",function($idproducty){
       
    User::verifylogin();
 
    $producty = new Products();
 
    $producty->get((int)$idproducty);
 
    $producty->setData($_POST);
 
    $producty->save();
 
    $producty->setPhoto($_FILES["file"]);
 
    header("Location: /admin/products");
    exit;
 
 
 
 });
 
?>