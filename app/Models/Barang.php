<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_barang';

    protected $primaryKey = 'id';
    
    public $incrementing = true;

    public $timestamps = true;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'harga',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class, 'kode_barang', 'id');
    }
}
