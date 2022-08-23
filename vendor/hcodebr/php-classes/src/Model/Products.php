<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
class Products extends Model{

    

    

  
     public static function listAll(){
         $sql = new Sql();
         return $sql->select('select * from tb_products ORDER BY desproduct');
     }

     public function save()
     {
        
            $sql = new Sql();
            $result = $sql->select("CALL sp_products_save(:idproduct,:desproduct,:vlprice,:vlwidth,:vlheight,:vllength,:vlweight,:desurl)",array(
                ":idproduct"=>$this->getidproduct(),
                ":desproduct"=>$this->getdesproduct(),
                ":vlprice"=>$this->getvlprice(),
                ":vlwidth"=>$this->getvlwidth(),
                ":vlheight"=>$this->getvlheight(),
                ":vllength"=>$this->getvllength(),
                ":vlweight"=>$this->getvlweight(),
                ":desurl"=>$this->getdesurl()
            ));
            
   var_dump($result);
            $this->setData($result[0]);

            Categories::updateFile();
        

     }

     public function get($idproduct){
        $sql = new Sql();
      $result = $sql->select("select * from tb_products where idproduct = :id",array(
          ":id"=>$idproduct
      ));
      
        $this->setData($result[0]);
     
    }
    

    public function delete(){
        $sql = new Sql();
        $sql->query("delete from tb_products where idproduct = :id",array(
            ":id"=>$this->getidproduct()
        ));

        Categories::updateFile();
    }


    public function checkPhoto()
    {
       if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."res".DIRECTORY_SEPARATOR."site".DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."products".DIRECTORY_SEPARATOR.$this->getidproduct().".jpg"))
       {
        $url  = "/res/site/img/products/".$this->getidproduct().".jpg";
       }else
       {
        $url = "/res/site/img/products/pro.jpg";
       }
        return $this->setdesphoto($url);

    }

    public function setPhoto($file)
{  

	$extension = explode(".",$file["name"]);
	$extension = end($extension);

	switch($extension)
	{
		case "jpg":
		case "jpeg":
		$image = imagecreatefromjpeg($file["tmp_name"]);
		break;
		case "gif" :
		$image = imagecreatefromgif($file["tmp_name"]);
		break;
		case "png" :
		$image = imagecreatefrompng($file["tmp_name"]);
		break;
	
	}

    $destino = $_SERVER['DOCUMENT_ROOT'].
		 DIRECTORY_SEPARATOR . "res" . 
		 DIRECTORY_SEPARATOR . "site" . 
		 DIRECTORY_SEPARATOR . "img" . 
		 DIRECTORY_SEPARATOR . "products" .
		 DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg";

	imagejpeg($image,$destino);

	imagedestroy($image);

	$this->checkPhoto();

}

    public function getValues()
    {

        $this->checkPhoto();
        
        $values = parent::getValues();

        return $values;
    }


    



}