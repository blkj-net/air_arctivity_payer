<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $openid
 * @property string $nickname
 * @property int $sex
 * @property string $language
 * @property string $city
 * @property string $province
 * @property string $country
 * @property string $headimgurl
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $access_token
 * @property string $refresh_token
 * @property string $scope
 * @property int $expires_in
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'openid',
        'nickname',
        'sex',
        'language',
        'city',
        'province',
        'country',
        'headimgurl',
        'created_at',
        'updated_at',
        'access_token',
        'refresh_token',
        'scope',
        'expires_in'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'sex'        => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_in' => 'integer'
    ];
}