<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    
    public static function group(string $prefix): array
    {
        $result = [];

        foreach (static::where('key', 'like', $prefix . '.%')->get() as $row) {
            $result[substr($row->key, strlen($prefix) + 1)] = $row->value;
        }

        return $result;
    }
}