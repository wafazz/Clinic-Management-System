<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'type', 'title', 'message',
        'icon', 'color', 'link', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getIsReadAttribute()
    {
        return $this->read_at !== null;
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public static function send($userId, $type, $title, $message, $options = [])
    {
        return self::create([
            'user_id' => $userId,
            'branch_id' => $options['branch_id'] ?? session('current_branch_id'),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $options['icon'] ?? self::getIconForType($type),
            'color' => $options['color'] ?? self::getColorForType($type),
            'link' => $options['link'] ?? null,
        ]);
    }

    public static function sendToRole($role, $type, $title, $message, $options = [])
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $type, $title, $message, $options);
        }
    }

    public static function sendToAll($type, $title, $message, $options = [])
    {
        $users = User::where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $type, $title, $message, $options);
        }
    }

    private static function getIconForType($type)
    {
        return match ($type) {
            'appointment' => 'mdi-calendar-clock',
            'invoice' => 'mdi-receipt',
            'pharmacy' => 'mdi-pill',
            'lab' => 'mdi-flask',
            'insurance' => 'mdi-shield-check',
            'patient' => 'mdi-account-multiple',
            default => 'mdi-bell',
        };
    }

    private static function getColorForType($type)
    {
        return match ($type) {
            'appointment' => 'info',
            'invoice' => 'success',
            'pharmacy' => 'warning',
            'lab' => 'danger',
            'insurance' => 'primary',
            'patient' => 'info',
            default => 'secondary',
        };
    }
}
