<?php

namespace App\Http\Controllers;




use App\Models\UserAccessToken;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Lumen\Http\Request;
use PHPUnit\Util\Json;


class AuthController extends Controller
{
    public $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->client = new Client(["base_uri" => config("app.api_BaseURL"),
            "headers" => ["Authorization" => config("app.api_token")],
            "http_errors" => false]);
    }

    //

    //登录
    public function login(Request $request){
        $body = $request->json();
        $username = $body->get("username");
        $password = $body->get("password");

        $Response = $this->client->post("/auth/check", [RequestOptions::JSON=>["username" => $username, "password" => $password]]);
        if($Response->getStatusCode() != 200){
            return new JsonResponse(["message" => "Login failed"], 403);
        }

        $AccessTokenObject = new UserAccessToken();
        $AccessTokenObject->username = $username;
        $AccessTokenObject->token = Str::random(128);
        $AccessTokenObject->dt_create = Carbon::now();
        $AccessTokenObject->dt_expire = Carbon::now()->addYear();

        $AccessTokenObject->save();
        return new JsonResponse(["message" => "Login successfully",
            "token" => $AccessTokenObject->token,
            "expire" => $AccessTokenObject->dt_expire->timestamp], 200);

    }

    public function changePassword(Request $request){
        $passwordNew = $request->json("password");
        if(Str::length($passwordNew) < 6){
            return new JsonResponse(["message" => "New password too short"]);
        }
        $username = Config::get("user.username");
        $this->client->post("/auth/changePassword", [RequestOptions::JSON=>["username"=>$username, "password"=>$passwordNew]]);
        return Response("", 200);
    }

    public function loginGame(Request $request){
        $reqBody = $request->json();
        $reqUsername = $reqBody->get("username");
        $username = Config::get("user.username");
        if($username != $reqUsername){
            return new JsonResponse(["message" => "Request parameter not available"], 403);
        }
        $resp = $this->client->post("/auth/forceLogin", [RequestOptions::JSON=>["username"=>$username]]);
        if ($resp->getStatusCode() == 200){
            return Response("", 200);
        }else{
            return new JsonResponse(["message" => "Failed to login"], 502);
        }
    }

    public function logoutAll(Request $request){
        $username = Config::get("user.username");
        UserAccessToken::where("username", $username)->where("dt_expire", ">", Carbon::now())->update(["dt_expire" => Carbon::now()]);

    }
}
