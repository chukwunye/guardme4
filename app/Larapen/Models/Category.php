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

use App\Larapen\Scopes\ActiveTranslationScope;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Category extends BaseModel
{
    use Sluggable, SluggableScopeHelpers;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    // protected $primaryKey = 'id';
    protected $appends = ['tid'];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;
    
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
        'parent_id',
        'name',
        'slug',
        'description',
        'picture',
        'css_class',
        'active',
        'lft',
        'rgt',
        'depth',
        'translation_lang',
        'translation_of',
        'type'
    ];
    protected $translatable = ['name', 'slug', 'description'];
    
    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    // protected $hidden = [];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = [];
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new ActiveTranslationScope());
        
        // before delete() method call this
        static::deleting(function ($ad) {
            // Delete all children categories
            $ad->children()->delete();
            
            // Delete all translated categories
            $ad->translated()->delete();
        });
    }
    
    public static function transById($id, $locale = '')
    {
        if (empty($locale)) {
            $locale = config('app.locale');
        }

		$cat = static::where('translation_of', $id)->where('translation_lang', $locale)->first();

        if (empty($cat)) {
            $cat = static::find($id);
        }
        
        return $cat;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_name',
            ],
        ];
    }
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function ads()
    {
        return $this->hasManyThrough('App\Larapen\Models\Ad', 'App\Larapen\Models\Category', 'parent_id', 'category_id');
    }
    
    public function children()
    {
        return $this->hasMany('App\Larapen\Models\Category', 'parent_id');
    }
    
    public function translated()
    {
        return $this->hasMany('App\Larapen\Models\Category', 'translation_of');
    }
    
    public function lang()
    {
        return $this->hasOne('App\Larapen\Models\Category', 'translation_of', 'abbr');
    }
    
    public function parent()
    {
        return $this->belongsTo('App\Larapen\Models\Category', 'parent_id');
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
    // The slug is created automatically from the "title" field if no slug exists.
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }
        
        return $this->name;
    }
    
    public function getTidAttribute()
    {
        if (!is_null($this->attributes['translation_of']) and $this->attributes['translation_of'] != '' and $this->attributes['translation_of'] != 0) {
            return $this->attributes['translation_of'];
        } else {
            return $this->attributes['id'];
        }
    }
    
    public function getTranslationOfAttribute()
    {
        if (!is_null($this->attributes['translation_of']) and $this->attributes['translation_of'] != '' and $this->attributes['translation_of'] != 0) {
            return $this->attributes['translation_of'];
        } else {
            return $this->attributes['id'];
        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
