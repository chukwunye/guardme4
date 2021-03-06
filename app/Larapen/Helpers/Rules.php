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

namespace App\Larapen\Helpers;

use App\Larapen\Models\Ad;
use App\Larapen\Models\Category;
use App\Larapen\Models\Pack;
use App\Larapen\Models\PaymentMethod;
use App\Larapen\Models\Resume;
use Illuminate\Http\Request;

class Rules
{
    public static $packs;
    public static $payment_methods;

	/**
	 * @param Request $request
	 * @param $method
	 * @return array
	 */
    public static function Ad(Request $request, $method)
    {
        $rules = [];
        
        // Create
        if (in_array($method, ['POST', 'CREATE'])) {
            $rules = [
                'category' 				=> 'required|not_in:0',
                'ad_type' 				=> 'required|not_in:0',
                'company_name' 			=> 'required|mb_between:10,200|whitelist_word_title',
				'company_description' 	=> 'required|mb_between:10,3000|whitelist_word',
				'title' 				=> 'required|mb_between:10,200|whitelist_word_title',
                'description' 			=> 'required|mb_between:10,3000|whitelist_word',
				'salary_type' 			=> 'required|not_in:0',
                'contact_name' 			=> 'required|mb_between:3,200',
                'contact_email' 		=> 'required|email|max:100|whitelist_email|whitelist_domain',
                'contact_phone' 		=> 'min:3|max:20',
                'location' 				=> 'required|not_in:0',
                'city' 					=> 'required|not_in:0',
            ];
            
            // Require 'pictures' if exists
            if ($request->file('logo')) {
				$rules['logo'] = 'required|image|mimes:jpeg,jpg,png';
            }
            
            // Require 'pack' & 'payment_method'
            if (isset(self::$packs) and isset(self::$payment_methods) and !self::$packs->isEmpty() and !self::$payment_methods->isEmpty()) {
                // Require 'pack' if Packs are available
                $rules['pack'] = 'required';

                // Require 'payment_method' if the Pack price > 0
                if ($request->has('pack')) {
                    $pack = Pack::find($request->input('pack'));
                    if (!is_null($pack) and $pack->price > 0) {
                        $rules['payment_method'] = 'required|not_in:0';
                    }
                }
            }
            
            // Recaptcha
            if (config('settings.activation_recaptcha')) {
                $rules['g-recaptcha-response'] = 'required';
            }
        }
        
        // Update
        if (in_array($method, ['PUT', 'PATCH', 'UPDATE'])) {
            $rules = [
                'category' 				=> 'required|not_in:0',
                'ad_type' 				=> 'required|not_in:0',
				'company_name' 			=> 'required|mb_between:10,200|whitelist_word_title',
				'company_description' 	=> 'required|mb_between:10,3000|whitelist_word',
                'title' 				=> 'required|mb_between:10,200|whitelist_word_title',
                'description' 			=> 'required|mb_between:10,3000|whitelist_word',
				'salary_type' 			=> 'required|not_in:0',
                'contact_name' 			=> 'required|mb_between:3,200',
                'contact_email' 		=> 'required|email|max:100|whitelist_email|whitelist_domain',
                'contact_phone' 		=> 'min:3|max:20',
            ];

			// Require 'pictures' if exists
			if ($request->file('logo')) {
				$rules['logo'] = 'required|image|mimes:jpeg,jpg,png';
			}
        }
        
        return $rules;
    }

	/**
	 * @param Request $request
	 * @return array
	 */
    public static function Signup(Request $request)
    {
        $rules = [
            'gender' 				=> 'required|not_in:0',
            'name' 					=> 'required|mb_between:3,200',
            'user_type' 			=> 'required|not_in:0',
            'country' 				=> 'sometimes|required|not_in:0',
            'phone' 				=> 'sometimes|required|min:3|max:20',
            'email' 				=> 'required|email|unique:users,email|whitelist_email|whitelist_domain',
            'password' 				=> 'required|between:5,15|confirmed',
            //'password' 			=> 'required|between:5,15', // @todo: delete this rule
            'g-recaptcha-response' 	=> (config('settings.activation_recaptcha')) ? 'required' : '',
            'term' 					=> 'accepted',
        ];

		// Check 'resume' is required
		if ($request->input('user_type') == 3) {
			$rules['resume'] = 'required|mimes:pdf,doc,docx,word,rtf,rtx,ppt,pptx,odt,odp,wps,jpeg,jpg,bmp,png';
		}
        
        return $rules;
    }

	/**
	 * @param Request $request
	 * @return array
	 */
    public static function Login(Request $request)
    {
        $rules = [
            'email' 	=> 'required|email',
            'password' 	=> 'required|min:5|max:50',
        ];
        
        return $rules;
    }

	/**
	 * @param Request $request
	 * @return array
	 */
    public static function ContactUs(Request $request)
    {
        $rules = [
            'first_name' 			=> 'required|mb_between:2,100',
            'last_name' 			=> 'required|mb_between:2,100',
            'email' 				=> 'required|email|whitelist_email|whitelist_domain',
            'message' 				=> 'required|mb_between:5,500',
            'g-recaptcha-response' 	=> (config('settings.activation_recaptcha')) ? 'required' : '',
        ];
        
        return $rules;
    }

	/**
	 * @param Request $request
	 * @return array
	 */
    public static function Message(Request $request)
    {
        $rules = [
            'sender_name' 			=> 'required|mb_between:3,200',
            'sender_email' 			=> 'required|email|max:100',
            'sender_phone' 			=> 'required|max:50',
            'message' 				=> 'required|mb_between:20,500',
            'g-recaptcha-response' 	=> (config('settings.activation_recaptcha')) ? 'required' : '',
            'ad' 					=> 'required|numeric',
        ];

        // Check 'resume' is required
        if (\Auth::check()) {
            $resume = Resume::where('user_id', \Auth::user()->id)->first();
            if (empty($resume) or trim($resume->filename) == '' or !file_exists(public_path() . '/uploads/resumes/' . $resume->filename)) {
                $rules['resume'] = 'required|mimes:pdf,doc,docx,word,rtf,rtx,ppt,pptx,odt,odp,wps,jpeg,jpg,bmp,png';
            }
        } else {
            $rules['resume'] = 'required|mimes:pdf,doc,docx,word,rtf,rtx,ppt,pptx,odt,odp,wps,jpeg,jpg,bmp,png';
        }
        
        return $rules;
    }

	/**
	 * @param Request $request
	 * @return array
	 */
    public static function Report(Request $request)
    {
        $rules = [
            'report_type' 			=> 'required|not_in:0',
            'report_sender_email' 	=> 'required|email|max:100',
            'report_message' 		=> 'required|mb_between:20,500',
            'ad' 					=> 'required|numeric',
            // @fixme : multi-captcha on the same page
            //'g-recaptcha-response'  => (config('settings.activation_recaptcha')) ? 'required' : '',
        ];
        
        return $rules;
    }

	/**
	 * @param Request $request
	 * @return array
	 */
	public static function SendAdByEmail(Request $request)
	{
		$rules = [
			'sender_email' 		=> 'required|email|max:100',
			'recipient_email' 	=> 'required|email|max:100',
			//'message' 		=> 'required|mb_between:20,500',
			'ad' 				=> 'required|numeric',
		];

		return $rules;
	}
}
