<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Payment;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(){

         $defaultPaymentDateQuery = Configuration::where(
        'type', 'PAYMENT_DATE', )->first();
        $defaultPaymentDate = $defaultPaymentDateQuery->value;
        $convertedPaymentDate = intval($defaultPaymentDate);
        $today = date('d');

        $isPaymentDay = false;

        if($today == $convertedPaymentDate ){
            $isPaymentDay = true;
        }

        $payments = Payment::latest()->orderBy('id','desc')->paginate(10);
        return view('payments.index', compact('payments', 'isPaymentDay'));
    }
}
