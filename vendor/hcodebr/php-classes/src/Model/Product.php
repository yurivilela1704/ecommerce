<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Product extends Model
{
    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

    }

    public static function checkList($list)
    {

        foreach ($list as &$row) {

            $product = new Product();
            $product->setData($row);
            $row = $product->getValues();
        }
        return $list;
    }

    public function save()
    {
        $sql = new Sql();

        $result = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, 
        :vlheight, :vllength, :vlweight, :desurl)", [
            ":idproduct" => $this->getidproduct(),
            ":desproduct" => $this->getdesproduct(),
            ":vlprice" => $this->getvlprice(),
            ":vlwidth" => $this->getvlwidth(),
            ":vlheight" => $this->getvlheight(),
            ":vllength" => $this->getvllength(),
            ":vlweight" => $this->getvlweight(),
            ":desurl" => $this->getdesurl()
        ]);

        $this->setData($result[0]);

    }

    public function getProduct($idproduct)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct;",
            [":idproduct" => $idproduct]
        );

        $data = $results[0];

        $this->setData($data);

    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct;",
            [":idproduct" => $this->getidproduct()]
        );

    }

    public function checkPhoto()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
            "resources" . DIRECTORY_SEPARATOR .
            "site" . DIRECTORY_SEPARATOR .
            "img" . DIRECTORY_SEPARATOR .
            "products" . DIRECTORY_SEPARATOR .
            $this->getidproduct() . ".jpg"))
        {
            $url = "/resources/site/img/products/" . $this->getidproduct().".jpg";
        } else
        {
            $url = "/resources/site/img/product.jpg";
        }
        return $this->setdesphoto($url);
    }

    public function getValues()
    {

        $this->checkPhoto();

        $values = parent::getValues();

        return $values;
    }

    public function setPhoto($file)
    {
        $extension = explode('.', $file["name"]);
        $extension = end($extension);

        switch ($extension)
        {
            case "jpg":
            $image = imagecreatefromjpeg($file["tmp_name"]);
            break;

            case "gif":
            $image = imagecreatefromgif($file["tmp_name"]);
            break;

            case "png":
            $image = imagecreatefrompng($file["tmp_name"]);
            break;
        }

        $destino = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
            "resources" . DIRECTORY_SEPARATOR .
            "site" . DIRECTORY_SEPARATOR .
            "img" . DIRECTORY_SEPARATOR .
            "products" . DIRECTORY_SEPARATOR .
            $this->getidproduct().".jpg";

        imagejpeg($image, $destino);

        imagedestroy($image);

        $this->checkPhoto();
    }

    public function getFromURL($desurl)
    {
        $sql = new Sql();

        $rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1;", [
            ":desurl"=>$desurl
        ]);

        $this->setData($rows[0]);

    }

    public function getCategories()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_categories 
            INNER JOIN tb_productscategories 
            ON tb_categories.idcategory = tb_productscategories.idcategory
            WHERE tb_productscategories.idproduct = :idproduct", [
                ":idproduct"=>$this->getidproduct()
    ]);
    }
}
