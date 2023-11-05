<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;
use Predis\Client;

class RedisCheckByKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = new Client([
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST','192.168.0.2'),
            'port' => env('REDIS_PORT',6379),
            'password' => env('REDIS_PASSWORD','Gf4ezYLeNB32zvtTpkFQD/co0D8ZnrJKoqTbMBiyyQfbpEMyq8sZSy69MquluZIh$')
        ]);

        $token = $request->bearerToken();

        if (!$token) {
            return response('Unauthorized', 401);
        }

        if ($client->exists($token)) {

            $userId = $client->get($token);
            dd($userId);

            // Manually authenticate the user
            $user = User::find($userId);
            if ($user) {
                Auth::login($user);
                return $next($request);
            }
        }

        return response('Unauthorized', 401);
    }
}
