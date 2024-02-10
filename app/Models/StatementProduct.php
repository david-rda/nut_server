<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Statement;
use App\Models\Product;

class StatementProduct extends Model
{
    use HasFactory;

    protected $table = "statement_products";

    protected $primaryKey = "id";

    protected $fillable = [
        "statement_id",
        "product_id",
        "price",
    ];

    protected $dates = [
        "deleted_at"
    ];

    protected $appends = [
        "products"
    ];

    protected $hidden = [
        "product"
    ];

    public $timestamps = true;

    public function statement() {
        return $this->belongsTo(Statement::class, "id", "statement_id");
    }

    public function product() {
        return $this->hasMany(Product::class, "id", "product_id")->where("status", "enabled");
    }

    public function getProductsAttribute() {
        return $this->product;
    }
}
