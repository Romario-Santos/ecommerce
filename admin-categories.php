<?php
use \Hcode\PageAdmin;
use \Hcode\Page;
use \Hcode\Model\categories;
use \Hcode\Model\User;

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

$app->get("/category/:idcategory",function($idcategory)
{
	$data = new DateTime();
	$dados = [
		"data"=>[
			"autor"=>"Romario Santos",
			"email"=>"Romariocb2@gmail.com",
			"dataAtual"=>$data->format("d/m/Y")
		]
	];


   $categories = new Categories();

   $categories->get((int)$idcategory);

   $page = new Page($dados);
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$page->setTpl("category",[
		"category"=>$categories->getValues(),
		"products"=>[]
	]);


});


?>