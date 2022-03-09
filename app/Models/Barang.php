<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'category_id', 'quantity'
    ];

    public function categorybarang()
    {
        return $this->belongsTo(categorybarang::class, 'category_id');
    }
    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }
    public function barangkeluar()
    {
        return $this->hasMany(barangkeluar::class);
    }
    public function barangmasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }
}
