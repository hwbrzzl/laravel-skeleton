<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use MatrixLab\LaravelAdvancedSearch\AdvancedSearchTrait;
use MatrixLab\LaravelAdvancedSearch\WithAndSelectForGraphQLGeneratorTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use SoftDeletes, AdvancedSearchTrait, WithAndSelectForGraphQLGeneratorTrait;

    protected $fillable = [
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
