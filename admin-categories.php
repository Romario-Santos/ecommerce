<?php
use \Hcode\PageAdmin;
use \Hcode\Page;
use \Hcode\Model\categories;
use \Hcode\Model\User;
use \Hcode\Model\Products;

/**trabalhando categorias */
$app->get("/admin/categories/:idcategory/delete",function($idcategory){
	User::verifyLogin();
	$category = new Categories();
	$category->get((int)$idcategory);
	$category->delete();
    header("Location: /admin/categories");
	exit;


});

$app->get('/admin/categories',function(){
	User::verifyLogin();
	$categories = Categories::listAll();
	$page = new PageAdmin();

	$page->setTpl("categories",array(
		"categories"=>$categories
	));

});

$app->get("/admin/categories/create",function(){
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});


$app->post("/admin/categories/create",function(){
	User::verifyLogin();
    $categories = new Categories();
	$categories->setData($_POST);
	$categories->save();

	header("location: /admin/categories");
	exit;
	
});

$app->get("/admin/categories/:idcategory",function($idcategory){
	User::verifyLogin();

	$category = new Categories();
	$category->get((int)$idcategory);
	$page = new PageAdmin();

	$page->setTpl("categories-update",["category"=>$category->getValues()]);
	
});

$app->post("/admin/categories/:idcategory",function($idcategory){
	User::verifyLogin();

	$category = new Categories();
	$category->get((int)$idcategory);
	$category->setData($_POST);
	$category->save();
	
	header("location: /admin/categories");
	exit;
});



$app->get("/admin/categories/:idcategory/products",function($idcategory)
{
    User::verifyLogin();

	$category = new Categories();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products",[
		"category"=>$category->getValues(),
		"productsRelated"=>$category->getProducts(),
		"productsNotRelated"=>$category->getProducts(false)
	]);

});


$app->get("/admin/categories/:idcategory/products/:idproduct/add",function($idcategory,$idproduct)
{ 
	User::verifyLogin();

	$category = new Categories();

	$category->get((int)$idcategory);

	$product = new Products();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;

});

$app->get("/admin/categories/:idcategory/products/:idproduct/remove",function($idcategory,$idproduct)
{
	User::verifyLogin();

	$category = new Categories();

	$category->get((int)$idcategory);

	$product = new Products();

	$product->get((int)$idproduct);

	$category->removeProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
	
});
?>