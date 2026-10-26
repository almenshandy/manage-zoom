<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Helper untuk mengambil nilai setting dari cache/DB
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("sys_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Helper untuk menyimpan / update nilai setting
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("sys_setting_{$key}");
    }

    /**
     * Helper untuk mengambil semua setting zoom dalam bentuk array terformat
     */
    public static function getZoomMeetingSettings(): array
    {
        return [
            'waiting_room'               => (bool) static::get('zoom_waiting_room', 1),
            'mute_upon_entry'            => (bool) static::get('zoom_mute_upon_entry', 1),
            'join_before_host'           => (bool) static::get('zoom_join_before_host', 0),
            'jbh_time'                   => (int) static::get('zoom_jbh_time', 5),
            'host_video'                 => (bool) static::get('zoom_host_video', 1),
            'participant_video'          => (bool) static::get('zoom_participant_video', 1),
            'auto_recording'             => static::get('zoom_auto_recording', 'none'),
            'meeting_authentication'     => (bool) static::get('zoom_meeting_authentication', 0),
            'watermark'                  => (bool) static::get('zoom_watermark', 0),
        ];
    }
}
