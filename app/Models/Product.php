<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StatementProduct;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    protected $primaryKey = "id";

    protected $fillable = [
        "name",
        "status"
    ];

    public $timestamps = true;
}
