<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get('/admin', function() {
    
	//verificar se esta logado
	User::verifyLogin();
	
	
	//quando chama o construct ele ja irar adicionar o header
	$pageAdmin = new PageAdmin();
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$pageAdmin->setTpl("index");

	//aqui ele termina a execução ele chama __destruct que cria o foot

});

//rota login 
$app->get("/admin/login",function(){

	$page = new PageAdmin(["header"=>false,"footer"=>false]);
	$page->setTpl("login");

});

$app->post("/admin/login",function(){
	
//recebe dados de login e senha verificar se existe na base caso exista criara a sessao e direcionara para rota admin
	User::login($_POST["login"],$_POST["password"]);

	//se deu certo login redireciona para pagina admin
	header("Location: /admin");
	exit;

});

$app->get("/admin/logout",function(){

	User::logout();

	header("Location: /admin/login");

	exit;

});


$app->get("/admin/forgot",function()
{
	$page = new PageAdmin(["header"=>false,"footer"=>false]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot",function()
{
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent",function(){


	$page = new PageAdmin(["header"=>false,"footer"=>false]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset",function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin(["header"=>false,"footer"=>false]);

	$page->setTpl("forgot-reset",array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset",function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = User::getPasswordHash($_POST["password"]);

	$user->setPassword($password);

	$page = new PageAdmin(["header"=>false,"footer"=>false]);

	$page->setTpl("forgot-reset-success");

});


?>