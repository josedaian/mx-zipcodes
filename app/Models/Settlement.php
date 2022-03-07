<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'zone_type', 'settlement_type_id', 'zip_code_id'];

    public function settlementType(){
        return $this->belongsTo(SettlementType::class);
    }

    public function zipcode(){
        return $this->hasOne(ZipCode::class);
    }
}
