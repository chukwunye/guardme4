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

class TransformInput
{
	/**
	 * @param $request
	 * @param Closure $next
	 * @return mixed
	 */
    public function handle($request, Closure $next)
    {
        if (in_array(strtolower($request->method()), ['post', 'put', 'patch'])) {
            $this->proccessBeforeValidation($request);
        }
        
        return $next($request);
    }

	/**
	 * @param $request
	 */
    public function proccessBeforeValidation($request)
    {
        $input = $request->all();
        
        // Format
        if ($request->has('description')) {
            $input['description'] = str_clean($request->input('description'));
        }
        if ($request->has('salary_min')) {
            $input['salary_min'] = preg_replace('/[^0-9\.]/', '', $request->input('salary_min'));
        }
		if ($request->has('salary_max')) {
			$input['salary_max'] = preg_replace('/[^0-9\.]/', '', $request->input('salary_max'));
		}
        if ($request->has('contact_phone')) {
            // Keep only numeric characters
            $input['contact_phone'] = preg_replace('/[^0-9]/', '', $request->input('contact_phone'));
        }
        
        if ($request->has('phone')) {
            // Keep only numeric characters
            $input['phone'] = preg_replace('/[^0-9]/', '', $request->input('phone'));
        }
        
        $request->replace($input);
    }
}
