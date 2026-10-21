<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'description',
        'subject_type', 'subject_id', 'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public static function record(string $action, string $description, ?Model $subject = null, ?int $userId = null): void
    {
        try {
            static::create([
                'user_id'      => $userId ?? auth()->id(),
                'action'       => $action,
                'description'  => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id'   => $subject?->getKey(),
                'ip_address'   => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e); 
        }
    }
}