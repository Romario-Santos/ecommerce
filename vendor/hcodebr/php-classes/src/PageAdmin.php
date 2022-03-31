<?php
namespace Hcode;

class PageAdmin extends Page{


    public function __construct($opts = array(),$tpl_dir = "/views/admin/"){
        //ja que nao mudamos nada no construtor so alteramos valor d aviaral tpl_dir chamomos o construtor da class pai e passamosos paramentros
        parent::__construct($opts,$tpl_dir);
    }



}