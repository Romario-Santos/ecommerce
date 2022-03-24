<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;

//chama as rotas 
$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    

	//quando chama o construct ele ja irar adicionar o header
	$page = new Page();
    
	//aqui ele chama o corpo da pagina
	$page->setTpl("index");

	//aqui ele termina a execução ele chama __destruct que cria o foot

});

$app->run();

 ?>