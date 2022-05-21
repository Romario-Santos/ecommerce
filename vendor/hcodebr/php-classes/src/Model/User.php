<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
class User extends Model{

    const SESSION = "user";
    const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";

    public static function login($login,$password){
        //cria objeto sql
     $sql = new Sql();
     //verifica se o login passado existe no banco fazemos o select caso exista armazenamos resultado em result
     $result = $sql->select("Select * from tb_users where deslogin = :LOGIN",array(
         ":LOGIN"=>$login
     ));
       if(count($result)===0){
          throw new \Exception("Usuario inexistente ou senha Invalida", 1);
          
       }

       //login existe pegamos o resultado na posição 0 e atribuimos a data
       $data = $result[0];

       //usar função password_verify e compara se a senha passada via post e igual a senha armazenada no  banco 
       if(password_verify($password,$data["despassword"]) === true){


        $user = new User();

        $user->setData($data);

        $_SESSION[User::SESSION] = $user->getValues();
        return $user;

       }else{

        throw new \Exception("Usuario inexistente ou senha Invalida", 1);
       
    }
    }

    public static function verifyLogin($inadmin = true){

        if(
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0 
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
        ){
           header("Location: /admin/login");
           exit;
        }

    }

    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
     }

     public static function listAll(){
         $sql = new Sql();
         return $sql->select('select * from tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson');
     }


     public function save(){
         $sql = new Sql();
         $result = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
             ":desperson"=>$this->getdesperson(),
             ":deslogin"=>$this->getdeslogin(),
             ":despassword"=>User::getPasswordHash($this->getdespassword()),
             ":desemail"=>$this->getdesemail(),
             ":nrphone"=>$this->getnrphone(),
             ":inadmin"=>$this->getinadmin()
         ));

         $this->setData($result[0]);
     }

     public function get($idusuario){
         $sql = new Sql();
       $result = $sql->select("select * from tb_users a INNER JOIN tb_persons b USING(idperson) where b.idperson = :id",array(
           ":id"=>$idusuario
       ));
       #var_dump($result);
       
    if(count($result)>0){
         $this->setData($result[0]);
      }
     }

     public function update(){


        $sql = new Sql();
         $result = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
             ":iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
             ":deslogin"=>$this->getdeslogin(),
             ":despassword"=>$this->getdespassword(),
             ":desemail"=>$this->getdesemail(),
             ":nrphone"=>$this->getnrphone(),
             ":inadmin"=>$this->getinadmin()
         ));

         $this->setData($result[0]);
     }

     public function delete(){
         $sql = new Sql();
         $sql->query("CALL sp_users_delete(:iduser)",array(
             ":iduser"=>$this->getiduser()
         ));
     }

     public static function getForgot($email)
     {
         $sql = new Sql();

         $result = $sql->select("select * from tb_persons a inner join tb_users b USING(idperson) where desemail = :email",array(
             ":email"=>$email
         ));

         if(count($result[0]) === 0)
         {
             throw new \Exception("Não foi possivel recuperar a senha!");
             
         }else{

            $data = $result[0];
           
            $result2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:id,:ip)",array(
                ":id"=>$data["iduser"],
                ":ip"=>$_SERVER["REMOTE_ADDR"]
            ));

            if(count($result2[0]) === 0 ){

                throw new \Exception("Não foi possivel recuperar a senha!");
                
            }else{
                $dataRecovery = $result2[0];
                $code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

                $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

                $mailer = new Mailer($data["desemail"],$data["desperson"],"Redefinir Senha da Hcode Store","forgot",array(
                    "name"=>$data["desperson"],
                    "link"=>$link
                ));

                $mailer->send();

                return $data;
            }
         }
     }

     public static function validForgotDecrypt($code)
     {
        $codeDesCript = base64_decode($code);
        $idrecovery = openssl_decrypt($codeDesCript, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));
        

        /*
        faremso uma query que irar consultar se o id existe na tabela recovery
        caso exista ele irar verificar a coluna dtrecovery esta null
        e se o registro tem menos de 1 hora que foi criado,
        caso tudo estaja ok 
        ele tara as informaçoes da tabela userpasswordrecoveri e tb usuario e tb pessoas
        de acordo como id passado
        */
        $sql = new Sql();
        $result = $sql->select("select *
        from tb_userspasswordsrecoveries a
        inner join tb_users b using(iduser)
        inner join tb_persons c using(idperson)
        where
          a.idrecovery = :idrecovery
          and
          a.dtrecovery is null
          and 
          DATE_ADD(a.dtregister, INTERVAl 1 HOUR) >= NOW();", array(
              ":idrecovery"=>$idrecovery
          ));

          if(count($result) === 0){
              throw new \Exception("Não foi possivel recupera a senha");
              
          }else{
              return $result[0];
          }
     }

     public static function setForgotUsed($idrecovery)
     {
         $sql = new Sql();

         $sql->query("update tb_userspasswordsrecoveries set dtrecovery = NOW() where idrecovery = :idrecovery",array(
             ":idrecovery"=>$idrecovery
         ));
     }

     public function setPassword($password)
     {
         $sql = new Sql();
         $sql->query("update tb_users set despassword = :password where iduser = :iduser",array(
             ":password"=>$password,
             ":iduser"=>$this->getiduser()
         ));
     }

     public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);

	}

}