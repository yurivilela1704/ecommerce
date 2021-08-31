<?php

namespace Hcode;

use Rain\Tpl;

class Page
{
    private  $tpl;
    private $options = [];
    private $defaults = [
        "header"=>true,
        "footer"=>true,
        "data" => []
    ];


    //primeiro metodo a ser executado ao instancionar o objeto dessa classe
    public function __construct($opts = array(), $tpl_dir = "/views/")
    {
        //merge ira juntar os dados de 2 arrays, caso os dados forem conflitantes, fica o do segundo parametro
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            // esse template resgata um script html e outro de cache
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . $tpl_dir,
            "chache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache",
            "debug" => false
        );
        Tpl::configure($config);

        $this->tpl = new Tpl;

        $this->setData($this->options["data"]);

        if ($this->options["header"] === true) $this->tpl->draw("header");
    }

    private function setData($data = [])
    {
        foreach ($data as $key => $value)
        {
            $this->tpl->assign($key, $value);
        }
    }

    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML);
    }

    //ultimo metodo a ser executado ao instancionar o objeto dessa classe
    public function __destruct()
    {
        if ($this->options["footer"] === true) $this->tpl->draw("footer");
    }
}
