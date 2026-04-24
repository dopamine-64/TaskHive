<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model {
    protected $fillable = ['action', 'entity_type', 'entity_id', 'description', 'admin_id'];
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
}