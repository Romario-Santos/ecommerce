<?php
namespace Hcode;
class Model{

    private $values = [];

    /**
     * usamemos o metodo especial __call para criar nosso set e get
     * dinamicamente uma vez que o medoto call recebe uma chamada de um metodo que nao existe na class
     */
    public function __call($namemethod,$args){
        /**
         * primeiro pegamos nome do metodo passado queremos descobri se e um set ou get 
         * para isso usamos  substr e pegamos 3 primerio caracteres e salvamos em method
         */
        $method = substr($namemethod,0,3);

        /**
         * agora pegamos o que vem depois do get ou set no nome do metodo chamado
         * para isso usamos substr soque sopegamos o que tem depois dos 3 priemrios caracteres
         */
        $fieldName = substr($namemethod,3,strlen($namemethod));

        switch($method){
            case "get":
                return $this->values[$fieldName];
                break;
            case "set":
                $this->values[$fieldName] = $args[0];
                break;
        }
    }


    /**
     * essa função ira carregar dados vindo do banco automaticamente na class
     * para isso rece o array com dados vindo do banco
     * e para cada item do array criamos um metodo set dinamicamente 
     * e atribuimos o valor 
     */
    public function setData($data = array()){
        foreach ($data as $key => $value) {
            /**
             * quando colocamos entre {} podemos 
             * podemos concaternar uma estring com variavel para 
             * realizar chamada de um metodo dinamicamente uma vez que 
             * não seria possivel sem concaternação
             * no caso como queremos criar metodo set 
             * colocamos o set e concaternamos com a chave do dados que veio do banco
             * assim criamos medoto set dinamiamente e atribuimos valor
             */
            $this->{"set".$key}($value);
        }
    }




    public function getValues(){
        return $this->values;
    }

}