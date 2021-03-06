<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\categories;




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
	$page = new Page($dados);
    
	//aqui ele chama o corpo da pagina passando umas variaveis 
	$page->setTpl("index");

	//aqui ele termina a execução ele chama __destruct que cria o foot

});

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



$app->get("/admin/users",function(){

	User::verifyLogin();

    $users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users",array(
			"users"=>$users
		));

});




$app->get("/admin/users/create",function(){

	User::verifyLogin();
	$page = new PageAdmin();

	$page->setTpl("users-create");

});
$app->post("/admin/users/create",function(){

	User::verifyLogin();
	
	$user = new User();

	$_POST['inadmin'] = (isset($_POST['inadmin']))?1:0;

	$user->setData($_POST);

	$user->save();

	header('Location: /admin/users');
	exit;

});

$app->get("/admin/users/:iduser/delete",function($iduser){

	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
    header("Location: /admin/users");
	exit;
	
});

$app->get("/admin/users/:iduser",function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);//pega usuario no banco usando o id

	$page = new PageAdmin();
//var_dump($user->getValues());
	$page->setTpl("users-update",array(
		"user"=>$user->getValues()
	));

});
$app->post("/admin/users/:iduser",function($iduser){

	User::verifyLogin();
	$user = new User();
	$_POST['inadmin'] = (isset($_POST['inadmin']))?1:0;
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
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



$app->run();

 ?>