<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZipCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['zip_code', 'locality', 'federal_entity_id', 'municipality_id'];

    public function federalEntity(){
        return $this->belongsTo(FederalEntity::class);
    }

    public function municipality(){
        return $this->belongsTo(Municipality::class);
    }

    public function settlements(){
        return $this->hasMany(Settlement::class);
    }
}
