<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = ['order_id', 'status', 'note', 'updated_by'];

    public function order() { return $this->belongsTo(Order::class); }
    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by'); }
}
