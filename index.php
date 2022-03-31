<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;

//chama as rotas 
$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$data = new DateTime();
    $dados = [
		"data"=>[
			"autor"=>"Romario Santos",
            "email"=>"Romariocb2@gmail.com",
			"dataAtual"=>$data->format("d/m/Y")
		]
	];
	
	//quando chama o construct ele ja irar adicionar o header
	$page = new Page();
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$page->setTpl("index",$dados);

	//aqui ele termina a execução ele chama __destruct que cria o foot

});

$app->get('/admin', function() {
    
	$data = new DateTime();
    $dados = [
		"data"=>[
			]
	];
	
	//quando chama o construct ele ja irar adicionar o header
	$pageAdmin = new PageAdmin();
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$pageAdmin->setTpl("index",$dados);

	//aqui ele termina a execução ele chama __destruct que cria o foot

});

$app->run();

 ?>