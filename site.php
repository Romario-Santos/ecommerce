<?php

use \Hcode\Page;

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
	$page = new Page($dados);
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$page->setTpl("index");

	//aqui ele termina a execução ele chama __destruct que cria o foot

});
?>