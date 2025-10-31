<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PembelianDetail;

class Pembelian extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_pembelian';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $fillable = [
        'tanggal',
        'keterangan',
        'total_harga',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'total_harga' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function details(){
        
        return $this->hasMany(PembelianDetail::class, 'pembelian_id', 'id')
                    ->whereNull('deleted_at');
    }
}
