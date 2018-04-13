<?php
/**
 * JobClass - Geo Classified Ads Software
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Larapen\Middleware;

use Closure;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Auth;

class XSSProtection
{
    /**
     * The following method loops through all request input and strips out all tags from
     * the request. This to ensure that users are unable to set ANY HTML within the form
     * submissions, but also cleans up input.
     *
	 * @param HttpRequest $request
	 * @param Closure $next
	 * @return mixed
	 */
    public function handle(HttpRequest $request, Closure $next)
    {
        if (Request::segment(1) == config('backpack.base.route_prefix', 'admin')) {
            if (Auth::check() and Auth::user()->is_admin == 1) {
                return $next($request);
            }
        }
        
        /*
        // Only POST, PUT & PATCH methods. Comment this condition to match all methods.
        if (!in_array(strtolower($request->method()), ['post', 'put', 'patch'])) {
            return $next($request);
        }
        */
        
        $input = $request->all();
        
        array_walk_recursive($input, function (&$input) use ($request) {
            $input = strip_tags($input);
            // Encode values from GET parameter
            if (in_array(strtolower($request->method()), ['get'])) {
                //$input = utf8_encode($input); // @todo: delete me
            }
        });
        
        $request->merge($input);
        
        return $next($request);
    }
}
