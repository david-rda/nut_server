<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StatementProduct;
use App\Models\StatementLog;

class Statement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "statements";

    protected $primaryKey = "id";

    protected $fillable = [
        'user_id',
        "operator_id",
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
        "statement_products",
        "operator",
        "logs",
        "company_name"
    ];

    protected $hidden = [
        "statement_product",
        "user",
        "log",
        "company_user"
    ];

    public $timestamps = true;

    public function setOverHeadAttribute($value) {
        $this->attributes["overhead_date"] = $this->asDateTime($value)->setTimezone('Asia/Tbilisi')->format("Y-m-d");
    }

    public function statement_product() {
        return $this->hasMany(StatementProduct::class, "statement_id", "id");
    }

    public function log() {
        return $this->hasMany(StatementLog::class, "statement_id", "id");
    }

    public function user() {
        return $this->hasOne(User::class, "id", "operator_id")->where("permission", "like", "%operator%");
    }

    public function company_user() {
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function getStatementProductsAttribute() {
        return $this->statement_product;
    }

    public function getOperatorAttribute() {
        return $this->user;
    }

    public function getLogsAttribute() {
        return $this->log;
    }

    // public function getCreatedAtAttribute($value) {
    //     return $value->format("Y-m-d");
    // }

    // public function getUpdatedAtAttribute($value) {
    //     return $value->format("Y-m-d");
    // }

    public function getCompanyNameAttribute() {
        return $this->company_user->company_name;
    }
}
