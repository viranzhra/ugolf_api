<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trx extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan
    protected $table = 'trx';

    // Tentukan kolom yang bisa diisi (fillable)
    protected $fillable = [
        'terminal_id',
        'trx_code',
        'trx_reff',
        'payment_type_id',
        'amount',
        'qty',
        'total_amount',
        'paycode',
        'expire',
        'trx_date',
        'payment_date',
        'payment_name',
        'payment_phone',
        'reffnumber',
        'issuer_reffnumber',
        'payment_status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
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

    // Relasi dengan model User (membuat transaksi)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi dengan model User (yang memperbarui transaksi)
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi dengan model User (yang menghapus transaksi)
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Scope untuk transaksi yang belum dihapus (soft delete)
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}

