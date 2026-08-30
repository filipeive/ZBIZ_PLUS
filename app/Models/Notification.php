<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'message', 'type', 'icon', 'read', 'action_url'];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Marcar como lida
    public function markAsRead()
    {
        if (!$this->read) {
            $this->update(['read' => true, 'read_at' => now()]);
        }
    }
}