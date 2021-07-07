<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $sn
 * @property string $body
 * @property int $total_fee
 * @property string $create_ip
 * @property int $status
 * @property string $platform
 * @property string $channel
 * @property string $openid
 * @property string $pay_notify_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $client
 * @property \Carbon\Carbon $pay_time
 */
class Order extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'sn',
        'body',
        'total_fee',
        'create_ip',
        'status',
        'platform',
        'channel',
        'openid',
        'created_at',
        'updated_at',
        'client',
        'pay_time',
        'pay_notify_url'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'total_fee'  => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'client'     => 'integer',
        'pay_time'   => 'datetime'
    ];
}