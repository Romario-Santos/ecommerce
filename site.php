<?php

use \Hcode\Page;
use \Hcode\Model\Products;
use \Hcode\Model\Categories;

$app->get('/', function() {
    


	$products = Products::listAll();
   
	
	//quando chama o construct ele ja irar adicionar o header
	$page = new Page();
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$page->setTpl("index",[
		"products"=>Products::checkList($products)
	]);

	//aqui ele termina a execução ele chama __destruct que cria o foot

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
		"products"=>Products::checkList($categories->getProducts())
	]);


});
?>