<?php

namespace App\Rules;

use App\Loan;
use App\Client;
use Illuminate\Contracts\Validation\Rule;

class HasNoUnusedDependent implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $r = request()->all();
        $loan = Loan::find($r['loan_id']);
        return Client::where('client_id',$value)->first()->hasUnusedDependent();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Client has no applied dependent';
    }
}
