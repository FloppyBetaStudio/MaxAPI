<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Js;
use Laravel\Lumen\Http\Request;
use PHPUnit\Util\Json;

class PlayerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $client;
    public function __construct()
    {
        //
        $this->client = new Client(["base_uri" => config("app.api_BaseURL"),
            "headers" => ["Authorization" => config("app.api_token")],
            "http_errors" => false]);
    }

    //获取玩家余额
    public function GetBalance(){
        $username = Config::get("user.username");
        $resp = $this->client->get("/player/balance/".$username);
        if($resp->getStatusCode() == 200){
            return new JsonResponse([
                "balance"=>$resp->getBody()->getContents(),
                "timestamp"=>Carbon::now()->timestamp
            ]);
        }else{
            return Response("", 500);
        }
    }


    //打钱
    public function PayMoney(Request $request, $targetUsername){
        $username = Config::get("user.username");
        $amount = $request->json()->get("amount");

        //检查数据合法性
        if($username == $targetUsername){
            return new JsonResponse(["message" => "You cannot pay to yourself"], 403);
        }
        if($amount <= 0){
            return new JsonResponse(["message" => "Invalid amount"], 403);
        }

        //检查目标用户是否存在
        $resp = $this->client->get("/player/isRegistered/".$targetUsername);
        if ($resp->getStatusCode() != 200){
            return new JsonResponse(["message" => "Target user not registered"], 404);
        }

        //检查余额是否足够
        $resp = $this->client->get("/player/balance/".$username);
        if (floatval($resp->getBody()->getContents()) < $amount){
            return new JsonResponse(["message" => "Not enough balance"], 403);
        }

        //扣钱（前面两个API没问题，这里理应没有问题）
        $this->client->delete("/player/balance/".$username, [RequestOptions::BODY=>$amount]);
        $this->client->put("/player/balance/".$username, [RequestOptions::BODY=>$amount]);

    }

    //在线人数列表
    public function PlayerList(Request $request){
        $resp = $this->client->get("/player/list");
        return Response($resp->getBody()->getContents(), 200);
    }
}
