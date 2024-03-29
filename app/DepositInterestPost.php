<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositInterestPost extends Model
{
    protected $fillable =['transaction_number','deposit_account_id','amount','balance','payment_method_id','repayment_date','office_id','paid_by','notes'];
    protected $appends = ['payment_method_name','formatted_amount','formatted_balance'];

    public function jv(){
        return $this->morphOne(JournalVoucher::class,'journal_voucherable');
    }
    public function transaction(){
        return $this->morphOne(Transaction::class,'transactionable');
    }

    public function getPaymentMethodNameAttribute(){
        return PaymentMethod::find($this->payment_method_id)->name;
    }
    public function getFormattedAmountAttribute(){
        return money($this->amount,2);
    }
    public function getFormattedBalanceAttribute(){
        return money($this->balance,2);
    }

    public function account(){
        return $this->belongsTo(DepositAccount::class,'deposit_account_id','id');
    }
}

