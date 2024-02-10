<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StatementProduct;

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

    protected $dates = [
        "deleted_at"
    ];

    protected $appends = [
        "statement_products"
    ];

    protected $hidden = [
        "statement_product"
    ];

    public $timestamps = true;

    public function setOverHeadAttribute($value) {
        $this->attributes["overhead_date"] = $this->asDateTime($value)->setTimezone('Asia/Tbilisi')->format("Y-m-d");
    }

    public function statement_product() {
        return $this->hasMany(StatementProduct::class, "statement_id", "id");
    }

    public function getStatementProductsAttribute() {
        return $this->statement_product;
    }
}
