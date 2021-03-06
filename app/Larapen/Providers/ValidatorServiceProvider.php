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

namespace App\Larapen\Providers;

use Illuminate\Support\ServiceProvider;
use App\Larapen\Helpers\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['validator']->extend('whitelist_domain', function ($attribute, $value) {
            return Validator::checkDomain($value);
        });
        
        $this->app['validator']->extend('whitelist_email', function ($attribute, $value) {
            return Validator::checkEmail($value);
        });
        
        $this->app['validator']->extend('whitelist_word', function ($attribute, $value) {
            return Validator::checkWord($value);
        });
        
        $this->app['validator']->extend('whitelist_word_title', function ($attribute, $value) {
            return Validator::checkTitle($value);
        });
        
        $this->app['validator']->extend('mb_between', function ($attribute, $value, $parameters, $validator) {
            return Validator::mbBetween($value, $parameters);
        });
    }
    
    public function register()
    {
        //
    }
}
