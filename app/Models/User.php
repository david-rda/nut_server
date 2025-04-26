<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // მოცემული ცვლადი განსაზღვრავს თუ რომელ ცხრილთან მუშაობს კონკრეტული მოდელი
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "email",
        "company_name",
        "identification_code",
        "legal_address",
        "actual_address",
        "personal_id",
        "status",
        "permission",
        "mobile",
        "password",
        "is_active"
    ];

    protected $primaryKey = "id";

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * მოცემული მოსავიი გამოიყენება იმისთვის, რომ მოხდეს ბაზის ველის
     * კონვერტირება სასურველ ფორმატში
     */
    protected $casts = [
        "is_active" => "integer"
    ];
}
