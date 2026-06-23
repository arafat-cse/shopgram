<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'phone', 'avatar', 'password', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders() { return $this->hasMany(Order::class); }
    public function cartItems() { return $this->hasMany(CartItem::class); }
    public function wishlist() { return $this->hasMany(Wishlist::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function addresses() { return $this->hasMany(Address::class); }
    public function tickets() { return $this->hasMany(SupportTicket::class); }
    public function returns() { return $this->hasMany(ReturnRequest::class); }
    public function stockHistories() { return $this->hasMany(StockHistory::class, 'created_by'); }

    public function defaultAddress() {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function isBlocked(): bool { return $this->status === 'blocked'; }
    public function isActive(): bool { return $this->status === 'active'; }
}
