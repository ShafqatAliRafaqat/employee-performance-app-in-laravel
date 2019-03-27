<?php

namespace App\Http\Middleware;

use Closure;
use App;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $locale = $request->route('locale');

        if($locale){

            App::setLocale($locale);

            $params = $request->route()->parameters;

            unset($params['locale']);

            $request->route()->parameters = $params;
        }

        return $next($request);
    }
}
