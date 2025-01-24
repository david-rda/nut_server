<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Attachements extends Model
{
    use HasFactory;

    protected $table = "attachements";

    protected $primaryKey = "id";

    protected $fillable = [
        "statement_id",
        "file",
        "name"
    ];

    public $timestamps = true;

    public function setFileAttribute($value)
    {
        return $this->attributes['file'] = DB::raw('CONVERT(VARBINARY(MAX), 0x' . bin2hex($value) . ')');
    }
}
