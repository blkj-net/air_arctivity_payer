<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $appid 
 * @property string $bank_type 
 * @property int $cash_fee 
 * @property string $fee_type 
 * @property string $is_subscribe 
 * @property int $mch_id 
 * @property string $nonce_str 
 * @property string $openid 
 * @property string $out_trade_no 
 * @property string $time_end
 * @property int $total_fee 
 * @property string $trade_type 
 * @property string $transaction_id 
 * @property \Carbon\Carbon $created_at 
 */
class PayNotify extends Model
{
    const UPDATED_AT =  null;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pay_notifys';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'appid', 'bank_type', 'cash_fee', 'fee_type', 'is_subscribe', 'mch_id', 'nonce_str', 'openid', 'out_trade_no', 'time_end', 'total_fee', 'trade_type', 'transaction_id', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'cash_fee' => 'integer', 'mch_id' => 'integer', 'total_fee' => 'integer', 'created_at' => 'datetime'];
}