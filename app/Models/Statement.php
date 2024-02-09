<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "statements";

    protected $primaryKey = "id";

    protected $fillable = [
        'user_id',
        "product_id",
        "status",
        "attachement_id",
        "overhead_number",
        "overhead_date",
        "store_address",
        "beneficiary_name",
        "card_number",
        "full_amount",
        "comment",
    ];

    public $timestamps = true;

    public function setOverHeadAttribute($value) {
        $this->attributes["overhead_date"] = $this->asDateTime($value)->setTimezone('Asia/Tbilisi')->format("Y-m-d");
    }
}
