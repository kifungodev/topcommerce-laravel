<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PusherCredentail;
class PusherCredential
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $pusher = PusherCredentail::first();
        $pusherConfig = [
            'driver' => 'pusher',
            'key' => $pusher->app_key,
            'secret' => $pusher->app_secret,
            'app_id' => $pusher->app_id,
            'options' => [
                'cluster' => $pusher->app_cluster,
                'useTLS' => true,
                'encrypted' => true
            ],
        ];
        // config(['broadcasting.connections.pusher' => $pusherConfig]);
        // dd(config('broadcasting.connections.pusher'));
        return $next($request);
    }
}
