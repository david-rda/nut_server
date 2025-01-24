<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Statement;

class StatementLog extends Model
{
    use HasFactory;

    protected $table = "statement_logs";

    protected $primaryKey = "id";

    protected $fillable = [
        "statement_id",
        "user_id",
        "operator_id",
        "comment"
    ];

    protected $appends = [
        "operator_data"
    ];

    protected $hidden = [
        "operator"
    ];

    public $timestamps = true;

    public function statement() {
        return $this->belongsTo(Statement::class, "id", "statement_id");
    }

    public function operator() {
        return $this->hasOne(User::class, "id", "operator_id")->where("permission", "like", "%operator%");
    }

    public function getOperatorDataAttribute() {
        return $this->operator;
    }
}
