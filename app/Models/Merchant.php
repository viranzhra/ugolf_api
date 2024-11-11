<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'merchants';
    protected $primaryKey = 'merchant_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'merchant_code',
        'merchant_name',
        'merchant_address',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'merchant_id' => 'integer',
        'merchant_code' => 'string',
        'merchant_name' => 'string',
        'merchant_address' => 'string',
        'description' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Relasi dengan model Terminal.
     */
    public function terminals()
    {
        return $this->hasMany(Terminal::class, 'merchant_id');
    }

    /**
     * Boot model untuk auto-generate merchant_code.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($merchant) {
            // Auto-generate merchant_code dengan format unik
            $merchant->merchant_code = 'MRC-' . strtoupper(uniqid());
        });
    }
}
