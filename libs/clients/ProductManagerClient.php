<?php
    namespace PandaStore\Clients;
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../Url.php";

    class ProductManagerService extends BaseClient {
        private $productManagerServiceUrl;
        function __construct($productManagerServiceUrl) {
            parent::__construct($productManagerServiceUrl);
            $this->productManagerServiceUrl = $productManagerServiceUrl;
        } 

        function mkProduct($name, $price, $photo_hash, $description, $tags) {
            return $this->call("mkProduct",
                ["name" => $name, 
                    "price" => $price,
                    "photo_hash" => $photo_hash,
                    "description" => $description,
                    "tags" => $tags],
                $this->productManagerServiceUrl);
        }

        function rmProduct($productId) {
            return $this->call("rmProduct", ["productId" => $productId], $this->productManagerServiceUrl);
        }

        function lsProduct($productCount, $pageNum) {
            return $this->call("lsProduct",
                ["productCount" => $productCount, "pageNum" => $pageNum],
                $this->productManagerServiceUrl);
        }
    }