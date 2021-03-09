<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    protected $fillable = ['journal_voucher_number','transaction_date','journal_voucherable_id','journal_voucherable_type','notes'];
    
    public function journal_voucherable(){
        return $this->morphTo();
    }

}
