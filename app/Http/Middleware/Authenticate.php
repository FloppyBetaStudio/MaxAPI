<?php

namespace App\Http\Middleware;

use App\Models\UserAccessToken;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use PHPUnit\Util\Json;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    //protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
//    public function __construct(Auth $auth)
//    {
//        $this->auth = $auth;
//    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$request->hasHeader("Authorization")){
            return new JsonResponse(["message"=>"Unauthorized"], 401);
        }
//        if ($this->auth->guard($guard)->guest()) {
//            return response('Unauthorized.', 401);
//        }
        $resp = UserAccessToken::where("token", $request->header("Authorization"));
        if ($resp->count() == 0 || $resp->count() > 1){
            return new JsonResponse(["message" => "Token invalid"], 401);
        }
        $resp = $resp->first();
        if($resp->dt_expire->lessThan(Carbon::now())){
            return new JsonResponse(["message" => "Token expired"], 401);
        }

        //后续懒得再获取用户名了
        Config::set("user.username", $resp->username);

        return $next($request);
    }
}
