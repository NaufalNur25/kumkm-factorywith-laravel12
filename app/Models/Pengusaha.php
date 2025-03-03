<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengusaha extends Model
{
    /** @use HasFactory<\Database\Factories\PengusahaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = "umkm_t_pengusaha";

    protected $primaryKey = "nik_pengusaha";
}
