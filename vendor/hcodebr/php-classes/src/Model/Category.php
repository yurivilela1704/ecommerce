<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Category extends Model
{
    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

    }

    public function save()
    {
        $sql = new Sql();

        $result = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", [
            ":idcategory" => $this->getidcategory(),
            ":descategory" => $this->getdescategory()
        ]);

        $this->setData($result[0]);

        Category::updateFile();
    }

    public function getCategory($idcategory)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory;",
            [":idcategory" => $idcategory]
        );

        $data = $results[0];

        $this->setData($data);

    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory;",
            [":idcategory" => $this->getidcategory()]
        );

        Category::updateFile();
    }

    public static function updateFile()
    {
        $categories = Category::listAll();

        $html = array();

        foreach ($categories as $row) {
            array_push($html, "<li><a href='/categories/". $row["idcategory"] . "'>" . $row["descategory"] . "</a></li>");
        }

        file_put_contents($_SERVER["DOCUMENT_ROOT"] .
            DIRECTORY_SEPARATOR . "views".
            DIRECTORY_SEPARATOR . "categories-menu.html",
            implode('', $html));
    }

    public function getProducts($related = true)
    {

        $sql = new Sql();

        if ($related === true)
        {
            return $sql->select("SELECT * FROM tb_products WHERE idproduct IN (
                    SELECT tb_products.idproduct from tb_products
                    INNER JOIN tb_productscategories ON tb_products.idproduct = tb_productscategories.idproduct
                    WHERE tb_productscategories.idcategory = :idcategory);", array(
                        ":idcategory"=>$this->getidcategory()
            ));
        } else
        {
            return $sql->select("SELECT * FROM tb_productS WHERE idproduct NOT IN (
                    SELECT tb_products.idproduct from tb_products
                    INNER JOIN tb_productscategories ON tb_products.idproduct = tb_productscategories.idproduct
                    WHERE tb_productscategories.idcategory = :idcategory);", array(
                ":idcategory" => $this->getidcategory()
            ));
        }
    }

    public function addProduct(Product $product)
    {
        $sql = new Sql();

        $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) 
                    VALUES (:idcategory, :idproduct);", [
            ":idcategory"=>$this->getidcategory(),
            ":idproduct"=>$product->getidproduct()
        ]);
    }

    public function removeProduct(Product $product)
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_productscategories 
                    WHERE idcategory = :idcategory AND idproduct = :idproduct);", [
            ":idcategory"=>$this->getidcategory(),
            ":idproduct"=>$product->getidproduct()
        ]);
    }

    //parte da paginação
    public function getProductsPage($actualPage = 1, $prodPerPage = 3)
    {
        $start = ($actualPage - 1) * $prodPerPage;

        $sql = new Sql();

        $prodResult =$sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_products
                        INNER JOIN tb_productscategories on tb_products.idproduct = tb_productscategories.idproduct
                        INNER JOIN tb_categories ON tb_categories.idcategory = tb_productscategories.idcategory
                        where tb_categories.idcategory = :idcategory
                        LIMIT $start, $prodPerPage;", [
            ":idcategory" => $this->getidcategory()
        ]);
        $totalResult = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            "data"=>Product::checkList($prodResult),
            "total"=>(int)$totalResult[0]["nrtotal"],
            "pages"=>ceil($totalResult[0]["nrtotal"] / $prodPerPage)
        ];
    }
}
