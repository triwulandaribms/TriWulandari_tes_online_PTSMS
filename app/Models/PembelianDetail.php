<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Barang;
use App\Models\Pembelian;

class PembelianDetail extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_pembelian_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
    
    protected $fillable = [
        'pembelian_id',
        'kode_barang', 
        'qty',
        'harga',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'id')
                    ->whereNull('deleted_at');
    }
}
