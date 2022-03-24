<?php
namespace Hcode;

use Rain\Tpl;

class Page{


    private $tpl;
    private $options = [];
    private $default = [
        "data"=>[]
    ];

    public function __construct($opts = array()){
        /**
         * pegamos o array de dados passado no construtor e mesclamo com o array default da class e atibuimos ao array options
         */
        $this->options = array_merge($this->default,$opts);
       
        /**
         * Definimos diretorio onde estara nosso template no caso diretorio viewes
         * e a pasta de cacher onde serao montado tamples antes da visualização do usuario no caso e o views-cacher
         */
        $config = array(
            "tpl_dir"=>$_SERVER["DOCUMENT_ROOT"]."/views/",
            "cache_dir"=>$_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"=>false
        );
        
        //passamos as configuraçoes para class Tpl
        Tpl::configure($config);
        
        //estacimos class Tpl
        $this->tpl = new Tpl;
        
        //fazemos o assing com dados que passamos
        $this->setData($this->options["data"]);

        //desenhamos o cabecalho
        $this->tpl->draw("header");
    }


    private function setData($data = array()){
        foreach ($data as $key => $value) {
            $this->tpl->assing($key,$value);
        }
    }

    public function setTpl($name,$data = array(),$returnHTML = false){
        /**
         * assinamos os dados que passamos como parametro
         */
        $this->setData($data);

        /**
         * desenhamos o tamplete que passamos 
         */
        return $this->tpl->draw($name,$returnHTML);
    }

    public function __destruct(){
        //ao para execução da class desenhamos o rodape
        $this->tpl->draw("footer");
    }

}