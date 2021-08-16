<?php

namespace App\Containers\UserSection\UserContainer\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Ship\Parents\Models\Model;
use Illuminate\Auth\Authenticatable;
use Laravel\Passport\HasApiTokens;

class UserContainer extends Model implements JWTSubject
{
    use HasApiTokens;
    use Authenticatable;

    protected $table = "user_containers";
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password'
    ];

    protected $attributes = [

    ];

    protected $hidden = [
        'rememberToken'
    ];

    protected $casts = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'UserContainer';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
