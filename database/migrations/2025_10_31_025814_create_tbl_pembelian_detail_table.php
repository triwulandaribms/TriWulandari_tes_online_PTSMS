<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_pembelian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('tbl_pembelian')->onDelete('cascade');
            $table->foreignId('kode_barang')->constrained('tbl_barang')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('harga', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pembelian_detail');
    }
};
