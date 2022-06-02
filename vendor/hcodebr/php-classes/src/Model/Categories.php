<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
class Categories extends Model{

    

    

  
     public static function listAll(){
         $sql = new Sql();
         return $sql->select('select * from tb_categories ORDER BY descategory');
     }

     public function save()
     {
        
            $sql = new Sql();
            $result = $sql->select("CALL sp_categories_save(:idcategory,:descategory)",array(
                ":idcategory"=>$this->getidcategory(),
                ":descategory"=>$this->getdescategory()
            ));
   
            $this->setData($result[0]);
        

     }

     public function get($idcategory){
        $sql = new Sql();
      $result = $sql->select("select * from tb_categories where idcategory = :id",array(
          ":id"=>$idcategory
      ));
      
        $this->setData($result[0]);
     
    }

    public function delete(){
        $sql = new Sql();
        $sql->query("delete from tb_categories where idcategory = :id",array(
            ":id"=>$this->getidcategory()
        ));
    }




}