<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $order_sn
 * @property string $refund_sn
 * @property int $total_fee
 * @property int $refund_fee
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $refund_at
 * @property int $status
 */
class OrderRefund extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_refunds';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'order_sn', 'refund_sn', 'total_fee', 'refund_fee', 'created_at', 'updated_at', 'refund_at', 'status', 'notify_url'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'total_fee' => 'integer', 'refund_fee' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'status' => 'integer'];
}