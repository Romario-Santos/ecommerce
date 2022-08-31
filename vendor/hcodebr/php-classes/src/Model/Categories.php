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

    public function getProducts($related = true)
    {
        $sql = new Sql();
        if($related === true)
        {
          return  $sql->select("
          select * from tb_products where idproduct in(
            select a.idproduct
            from tb_products a
            inner join tb_productscategories b on a.idproduct = b.idproduct
            where b.idcategory = :idcategory
            );
          ",array(
            "idcategory"=>$this->getidcategory()
          ));
        }else{
            return  $sql->select("
            select * from tb_products where idproduct not in(
                select a.idproduct
                from tb_products a
                inner join tb_productscategories b on a.idproduct = b.idproduct
                where b.idcategory = :idcategory
                );
                ",array(
                    ":idcategory"=>$this->getidcategory()
                ));
        }
        
    }

    public function addProduct(Products $products)
    {
        
       $sql = new Sql();
       $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory,:idproduct)",array(
       ":idcategory"=>$this->getidcategory(),
       ":idproduct"=>$products->getidproduct()));

    }

    public function removeProduct(Products $products)
    {
        
        $sql = new Sql();
        $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct",array(
        ":idcategory"=>$this->getidcategory(),
        ":idproduct"=>$products->getidproduct()));
    }

    public function getProductsPage($page = 1, $itensPerPage = 8)
    {
      //var_dump($page);
       
        $start = ($page - 1) * $itensPerPage;
        
        
        $sql = new Sql();

      $result = $sql->select("
      SELECT SQL_CALC_FOUND_ROWS *
FROM tb_products a
INNER JOIN tb_productscategories b on a.idproduct = b.idproduct
INNER JOIN tb_categories c on c.idcategory = b.idcategory
WHERE c.idcategory = :idcategory
LIMIT $start, $itensPerPage",[
    ":idcategory"=>$this->getidcategory()]);

    $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

    return [
        "data"=>Products::checkList($result),
        "total"=>(int)$resultTotal[0]["nrtotal"],
        "pages"=>ceil($resultTotal[0]["nrtotal"] / $itensPerPage)
    ];

    }

    


}