<?php


namespace App\Observers;


class RetailerObserver
{

    public function created(Retailer $retailer){
        $retailer->wallet()->create([
            'id' => random_int(0, 99),
            'amount' => 0]);
    }

}
