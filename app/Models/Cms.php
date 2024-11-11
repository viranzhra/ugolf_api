<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    use HasFactory;

    protected $table = 'cms'; // Nama tabel

    protected $primaryKey = 'cms_id'; // Primary key

    protected $fillable = [
        'terminal_id',
        'cms_code',
        'cms_name',
        'cms_value',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // Relasi dengan model Terminal
    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id');
    }
}
