<?php

use \Hcode\Page;
use \Hcode\Model\Products;

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
?>