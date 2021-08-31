<?php

namespace Hcode;

class PageAdmin extends Page
{
    public function __construct($opts = array(), $tpl_dir = "/views/admin/")
    {
        //herança na pratica
        parent::__construct($opts, $tpl_dir);
    }
}