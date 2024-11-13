<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan
    protected $table = 'payment_types';

    // Tentukan kolom yang bisa diisi (fillable)
    protected $fillable = [
        'payment_type_code',
        'payment_type_name',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Tentukan primary key
    protected $primaryKey = 'payment_type_id';

    // Tentukan jika tidak ada auto increment pada primary key
    public $incrementing = true;

    // Tentukan tipe data primary key (big integer)
    protected $keyType = 'int';

    // Relasi dengan model Config
    public function configs()
    {
        return $this->hasMany(Config::class, 'payment_type_id');
    }

    // Relasi dengan model Trx
    public function trx()
    {
        return $this->hasMany(Trx::class, 'payment_type_id');
    }
}
