<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{

    public $incrementing = true;

    protected $fillable = [
        'id', 'user_id', 'amount'
    ];

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
