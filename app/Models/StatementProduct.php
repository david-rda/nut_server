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
        "name",
    ];

    protected $hidden = [
        "product"
    ];

    public $timestamps = true;

    public function statement() {
        return $this->belongsTo(Statement::class, "id", "statement_id");
    }

    public function product() {
        return $this->hasOne(Product::class, "id", "product_id");
    }

    public function getNameAttribute() {
        return $this->product?->name;
    }
}