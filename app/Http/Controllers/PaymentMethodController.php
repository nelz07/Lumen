<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PaymentMethodController extends Controller
{


    //used for payment methods on paymentmethodcomponent
    public function fetchPaymentMethods(Request $request){
        
        if(in_array($request->payment_type,Schema::getColumnListing('payment_methods'))){
            $res['methods'] = PaymentMethod::where($request->payment_type, true)->orderBy('name','asc')->get(['name','id']);
            
            $default_payment_method_id = auth()->user()->office->first()->defaultPaymentMethods()[$request->payment_type];
            
            
            // $default_payment_method_id = auth()->user()->office->first()->defaultPaymentMethods()[$request->payment_type];
            // return $default_payment_method_id = auth()->user()->office->first()->defaultPaymentMethod();
            if($default_payment_method_id==null){
                $res['default_payment'] = array('id'=>null,'name'=>null);
                return $res;
            }
            $pm = PaymentMethod::find($default_payment_method_id);
            $res['default_payment'] = array('id'=>$pm->id,'name'=>$pm->name);
        
            return $res;
        }

        return view('pages.payment-method');
    }
}
