<?php
/**
 * JobClass - Geolocalized Job Board Script
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
use Illuminate\Support\Facades\App;

class HtmlMinify
{
	/**
	 * @param $request
	 * @param Closure $next
	 * @param int $cache
	 * @return mixed
	 */
    public function handle($request, Closure $next, $cache = 1)
    {
        $response = $next($request);
        
        // Don't minify the HTML in development environment
        if (config('settings.activation_minify_html') == 0) {
            return $response;
        }
        
        // Minify HTML
        $content = $response->getContent();
        $search = array(
            '/\>[^\S ]+/us',    // strip whitespaces after tags, except space
            '/[^\S ]+\</us',    // strip whitespaces before tags, except space
            '/(\s)+/us',        // shorten multiple whitespace sequences
        );
        $replace = array(
            '>',
            '<',
            '\\1',
        );
        $buffer = preg_replace($search, $replace, $content);
        
        return $response->setContent($buffer);
    }
}
