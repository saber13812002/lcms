<?php
/**
 * Laravel-cms - cms based on laravel
 *
 * @category  Laravel-cms
 * @package   Laravel
 * @author    Qiangzi <35924784@qq.com>
 * @date      2018/06/06 09:08:00
 * @copyright Copyright 2018 CMS
 * @license   https://opensource.org/licenses/MIT
 * @github    https://github.com/35924784/laravel-cms
 ***
 * @version   Release 1.0
 */

namespace App\Models;

/**
 * 设置模型
 *
 * Class Setting
 * @package App\Models
 */
class Setting extends Model
{
    protected $fillable = [
        'owner', 'module', 'section','key','value',
    ];

    public $timestamps = false;

    /**
     * 取出
     *
     * @param $section
     * @param string $module
     * @param string $owner
     * @return mixed
     */
    public function take($section, $module = 'common', $owner = 'system'){
        return static::where(['owner'=>$owner,'module'=>$module,'section'=>$section,])->pluck('value','key')->toArray();
    }


    /**
     * 存储
     *
     * @param $data
     * @param $section
     * @param string $module
     * @param string $owner
     * @return bool
     */
    public function store($data, $section, $module = 'common', $owner = 'system'){
        foreach($data as $key => $value){
            empty($value) && $value = '';
            static::updateOrCreate(['owner'=>$owner,'module'=>$module,'section'=>$section,'key'=>$key], ['value'=> is_string($value) ? $value : json_encode($value)]);
        }
        
        return static::clearCache();
    }
    
    /**
     * 清除缓存
     */
    public static function clearCache(){
        $key = 'settings_cache';
        
        \Cache::forget($key);
        
        return true;
    }
    
    /**
     * 获取所有数据
     *
     * @return mixed
     */
    public static function getStore(){

        $key = 'settings_cache';

        $settings = \Cache::get($key);

        if( \App::environment('production') && $settings ){
            return $settings;
        }

        $settings = static::get();

        if(\App::environment('production')){
            $expiredAt = now()->addMinutes(config('cache.expired.settings', 10));
            \Cache::put($key, $settings, $expiredAt);
        }

        return $settings;
    }

    /**
     * 将数据库中的配置信息注入到框架中
     */
    public static function afflux(){
        $config = [];
        foreach(static::getStore() as $item){
            $key = "{$item->owner}.{$item->module}.{$item->section}.{$item->key}";
            $config[$key] = $item->value;
        }

        config($config);
    }

}
