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

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontController;
use App\Larapen\Helpers\Arr;
use App\Larapen\Models\Language;
use \Illuminate\Support\Facades\Response;

class JsonController extends FrontController
{
	/**
	 * @return mixed
	 */
    public function getCountries()
    {
        exit(); // Moved to Post Ad's view
        $content = 'var countries = ' . (($this->countries) ? $this->countries->toJson() : '{}');
        $response = Response::make($content, 200);
        
        //$response->header('Content-Type', 'application/javascript'); // Don't active if file_get_contents() is used.
        return $response;
    }

	/**
	 * @return mixed
	 */
    public function getCategories()
    {
        exit();
        $categories = Language::find($this->country->lang->code)->categories()->where('parent_id', '=', 0)->orderBy('categories.lft')->get();
        
        $content = 'var categories = ' . (($categories) ? $categories->toJson() : '{}');
        $response = Response::make($content, 200);
        $response->header('Content-Type', 'application/javascript');
        
        return $response;
    }

	/**
	 * @return mixed
	 */
    public function getSubCategories()
    {
        exit();
        $sub_categories = Language::find($this->country->lang->code)->categories()->where('parent_id', '!=', 0)->orderBy('categories.lft')->get();
        
        $sub_categories = ($sub_categories) ? collect(Arr::groupBy($sub_categories->toArray(), 'id', false)) : false;
        $content = 'var subCategories = ' . (($sub_categories) ? $sub_categories->toJson() : '{}');
        $response = Response::make($content, 200);
        $response->header('Content-Type', 'application/javascript');
        
        return $response;
    }
}
