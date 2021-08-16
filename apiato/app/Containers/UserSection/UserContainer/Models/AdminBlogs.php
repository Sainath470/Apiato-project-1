<?php

namespace App\Containers\UserSection\UserContainer\Models;

use App\Ship\Parents\Models\Model;

class AdminBlogs extends Model 
{
    protected $table = 'container_blogs';

    protected $fillable = [
        'admin_id',
        'name',
        'type',
        'quantity',
        'price',
        'rating'
    ];

    protected $attributes = [

    ];

    protected $hidden = [

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
    protected string $resourceKey = 'AdminBlogs';

}
