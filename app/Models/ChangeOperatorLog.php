<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeOperatorLog extends Model
{
    use HasFactory;

    protected $table = "change_operator_logs";

    protected $primaryKey = "id";

    protected $fillable = [
        "statement_id",
        "operator_before",
        "operator_after",
        "coordinator_id"
    ];

    public $timestamps = true;
}
