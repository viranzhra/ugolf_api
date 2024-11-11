<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Terminal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'terminals';
    protected $primaryKey = 'terminal_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'merchant_id',
        'terminal_code',
        'terminal_name',
        'terminal_address',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'terminal_id' => 'integer',
        'merchant_id' => 'integer',
        'terminal_code' => 'string',
        'terminal_name' => 'string',
        'terminal_address' => 'string',
        'description' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Relasi dengan model Merchant.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    // Relasi dengan model Cms
    public function cms()
    {
        return $this->hasMany(Cms::class, 'terminal_id');
    }

    /**
     * Boot model untuk auto-generate terminal_code.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($terminal) {
            // Auto-generate terminal_code dengan format unik
            $terminal->terminal_code = 'TRM-' . strtoupper(uniqid());
        });
    }
}
