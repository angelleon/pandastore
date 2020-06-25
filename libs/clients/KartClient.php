<?php

namespace Pandastore\Clients;

require_once __DIR__ . "/BaseClient.php";



class KartClient extends BaseClient
{
    const KART_PUSH  = 0;
    const KART_POP   = 1;
    const KART_CLEAR = 2;
    const KART_GET   = 3;
    private $kartServiceUrl;

    public function __construct($kartServiceUrl)
    {
        parent::__construct($kartServiceUrl);
        $this->kartServiceUrl = $kartServiceUrl;
    }

    private function chkArgs($userId, $productId, $count)
    {
        if (!is_int($userId) || $userId <= 0) {
            return ["code" => -1, "msg" => "Invalid userId. Must be an integer greater than 0"];
        }
        if (!is_int($productId) || $productId <= 0) {
            return ["code" => -1, "msg" => "Invalid productId. Must be an integer greater than 0"];
        }
        if (!is_int($count) || $count <= 0) {
            return ["code" => -1, "msg" => "Invalid count. Must be an integer greater than 0"];
        }
        return 0;
    }

    public function push($userId, $productId, $count)
    {
        $error = $this->chkArgs($userId, $productId, $count);
        if ($error !== 0) {
            return $error;
        }
        return $this->call("kartPush", ["pushArgs" => ["userId" => $userId, "productId" => $productId, "count" => $count]], $this->kartServiceUrl);
    }

    public function pop($userId, $productId, $count)
    {
        $error = $this->chkArgs($userId, $productId, $count);
        if ($error !== 0) {
            return $error;
        }
        return $this->call("kartPop", ["popArgs" => ["userId" => $userId, "productId" => $productId, "count" => $count]], $this->kartServiceUrl);
    }

    public function clear($userId, $productId, $count)
    {
        $error = $this->chkArgs($userId, $productId, 1);
        if ($error !== 0) {
            return $error;
        }
        return $this->call("kartClear", ["clearArgs" => ["userId" => $userId, "productId" => $productId, "count" => 1]], $this->kartServiceUrl);
    }

    public function get($userId)
    {
        $error = $this->chkArgs($userId, 1, 1);
        if ($error !== 0) {
            return $error;
        }
        return $this->call("kartGet", ["getArgs" => ["userId" => $userId, "productId" => 1, "count" => 1]], $this->kartServiceUrl);
    }
}
