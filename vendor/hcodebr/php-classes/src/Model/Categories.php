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

            Categories::updateFile();
        

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

        Categories::updateFile();
    }


    /**
     * esse metodo pega a lista de categorias do banco
     * salva em um array 
     * passa por um foreach adicionando categorias com tags html ao array html
     * apos isso transforma o array html em uma string e salva no arquivo onde usaremos 
     * de forma statica no site, tudo isso para que a cada atualização de pagina
     * nao seja gasta consulta no banco, para rederizar as categorias
     */

    public static function updateFile()
    {
        $categories = Categories::listAll(); 
        $html = [];
        foreach ($categories as $row) {
            array_push($html,'<li><a href="/category/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categories-menu.html",implode('',$html));
    }




}