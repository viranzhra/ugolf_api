<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan
    protected $table = 'configs';

    // Tentukan kolom yang bisa diisi (fillable)
    protected $fillable = [
        'terminal_id',
        'payment_type_id',
        'config_merchant_id',
        'config_terminal_id',
        'config_pos_id',
        'config_user',
        'config_password',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Relasi dengan model Terminal
    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id');
    }

    // Relasi dengan model PaymentType
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    // Relasi dengan model User (pembuat konfigurasi)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi dengan model User (pengupdate konfigurasi)
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi dengan model User (penghapus konfigurasi)
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
