<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $incrementing = false;

    protected $table = 'transactions';

    protected $fillable = [
        'id', 'payee_id', 'payer_id', 'value'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);

    }

}
