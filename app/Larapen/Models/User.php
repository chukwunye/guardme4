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

namespace App\Larapen\Models;

use App\Larapen\Scopes\ActiveScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;

class User extends BaseUser
{
	use Notifiable;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    // protected $primaryKey = 'id';
    protected $appends = ['created_at_ta'];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_code',
        'user_type_id',
        'gender_id',
        'name',
        'about',
        'phone',
        'phone_hidden',
        'email',
        'password',
        'remember_token',
        'is_admin',
        'comments_enabled',
        'receive_newsletter',
        'receive_advice',
        'ip_addr',
        'provider',
        'provider_id',
        'activation_token',
        'active',
        'blocked',
        'closed',
    ];
    
    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'last_login_at', 'deleted_at'];
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new ActiveScope());
        
        // before delete() method call this
        static::deleting(function ($user) {
			// Delete all user's ads with dependencies
            if ($user->ads) {
                foreach ($user->ads as $item) {
                    $ad = Ad::find($item->id);
                    $ad->delete();
                }
            }
            
            // Delete all user's messages
            $user->messages()->delete();
            
            // Delete all favourite ads
            DB::table('saved_ads')->where('user_id', $user->id)->delete();
            
            // Delete all saved search
            $user->savedSearch()->delete();
        });
    }
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function ads()
    {
        return $this->hasMany('App\Larapen\Models\Ad', 'user_id');
    }
    
    public function country()
    {
        return $this->belongsTo('App\Larapen\Models\Country', 'country_code');
    }
    
    public function gender()
    {
        return $this->belongsTo('App\Larapen\Models\Gender', 'gender_id');
    }
    
    public function messages()
    {
        return $this->hasManyThrough('App\Larapen\Models\Message', 'App\Larapen\Models\Ad', 'user_id', 'ad_id');
    }
    
    public function savedAds()
    {
        return $this->belongsToMany('App\Larapen\Models\Ad', 'saved_ads', 'user_id', 'ad_id');
    }
    
    public function savedSearch()
    {
        return $this->hasMany('App\Larapen\Models\SavedSearch', 'user_id');
    }
    
    public function userType()
    {
        return $this->belongsTo('App\Larapen\Models\UserType', 'user_type_id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    public function getCreatedAtAttribute($value)
    {
        $value = \Carbon\Carbon::parse($value);
        if (session('time_zone')) {
            $value->timezone(session('time_zone'));
        }
        //echo $value->format('l d F Y H:i:s').'<hr>'; exit();
        //echo $value->formatLocalized('%A %d %B %Y %H:%M').'<hr>'; exit(); // Multi-language

        return $value;
    }
    
    public function getUpdatedAtAttribute($value)
    {
        $value = \Carbon\Carbon::parse($value);
        if (session('time_zone')) {
            $value->timezone(session('time_zone'));
        }

        return $value;
    }
    
    public function getLastLoginAtAttribute($value)
    {
        $value = \Carbon\Carbon::parse($value);
        if (session('time_zone')) {
            $value->timezone(session('time_zone'));
        }

        return $value;
    }
    
    public function getDeletedAtAttribute($value)
    {
        $value = \Carbon\Carbon::parse($value);
        if (session('time_zone')) {
            $value->timezone(session('time_zone'));
        }

        return $value;
    }
    
    public function getCreatedAtTaAttribute($value)
    {
        if (!isset($this->attributes['created_at']) and is_null($this->attributes['created_at'])) {
            return null;
        }

        $value = \Carbon\Carbon::parse($this->attributes['created_at']);
        if (session('time_zone')) {
            $value->timezone(session('time_zone'));
        }
        $value = time_ago($value, session('time_zone'), session('language_code'));

        return $value;
    }
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
