<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get all settings as key-value pairs
     */
    public static function getSettings($group = 'general')
    {
        $settings = self::where('group', $group)->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        // Default values if not set
        $defaults = [
            'contact_email' => 'support@luky.app',
            'contact_phone' => '+966 5XXXXXXXX',
            'contact_address' => 'Riyadh, Saudi Arabia',
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Update settings
     */
    public static function updateSettings(array $data, $group = 'general')
    {
        foreach ($data as $key => $value) {
            if ($value !== null) {
                self::updateOrCreate(
                    ['key' => $key, 'group' => $group],
                    ['value' => $value]
                );
            }
        }
        
        return true;
    }

    /**
     * Get a single setting value
     */
    public static function get($key, $default = null, $group = 'general')
    {
        $setting = self::where('key', $key)
                      ->where('group', $group)
                      ->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a single setting value
     */
    public static function set($key, $value, $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key, 'group' => $group],
            ['value' => $value]
        );
    }
}
