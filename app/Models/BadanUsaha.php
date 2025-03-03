<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BadanUsaha extends Model
{
    /** @use HasFactory<\Database\Factories\BadanUsahaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = "umkm_t_badan_usaha";

    protected $primaryKey = "id_data_badan_usaha";
}
