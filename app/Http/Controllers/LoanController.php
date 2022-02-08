<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\Reply;
use Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Get loan information from user and add to database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function apply(Request $request){
            $validated = $request->validate([
                'loan_amount' =>  'required|numeric',
                'loan_type' =>  'required|string',
                'loan_term' =>  'required|in:4,8,12,16,20,24',
                'loan_purpose' =>  'nullable|string',
            ]);

            $loan = new \App\Models\Loan();

            $loan->user_id = Auth::user()->id;
            $loan->final_amount = 0;
            $loan->loan_amount = $request->loan_amount;
            $loan->loan_type = $request->loan_type;
            $loan->loan_term = $request->loan_term;
            $loan->loan_purpose = $request->loan_purpose;
            $loan->status = 'pending';

            $loan->save();

            return Reply::success('Loan Applied Successfully.');
    }

    /**
     * Get Loans for the user loggedin with filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function loans(Request $request){
        $q = \App\Models\Loan::query();

        if($request->has('status')){
            $q->where('status', $request->status);
        }

        if($request->has('sort_by') && $request->sort_by == 'oldest'){
            $q->oldest();
        }else{
            $q->latest();
        }

        if($request->has('additionals') && count($request->additionals) > 0){
            $q->with($request->additionals);
        }

        $loans = $q->with('user')->paginate(25);

        return Reply::successData($loans);
    }

    /**
     * Get Loan details, Payments made for that lone.
     *
     * @param \App\Models\Loan $loan
     * @return \Illuminate\Http\Response
     */
    function loan($loan){
        $loan = \App\Models\Loan::where('user_id', Auth::user()->id)->where('id', $loan)->with(['payments'])->first();

        if($loan){
            return Reply::successData($loan);
        }else{
            return Reply::error('Loan not found.');
        }
    }

     /**
     * Get the Payment amount for the loan, check the loan date, if it is not on time then add penalty also
     *
     * @param \App\Models\Loan $loan_id
     * @param \App\Models\LoanPayment $payment_id
     * @return \Illuminate\Http\Response
     */
    function getPayment($loan_id, $payment_id){
        $payment = \App\Models\LoanPayment::where('loan_id', $loan_id)->where('id', $payment_id)->with('loan')->first();

        if($payment && $payment->loan->user_id == Auth::user()->id){
            if($payment->status == 'pending'){
                if($payment->payment_date <= Carbon::now()->format('Y-m-d')){
                   $payment->late_pay_charge = $payment->amount * 0.05;
                   $payment->on_time = 0;

                   $payment->save();
                }
            }

            $payment['final_amount'] = $payment->amount + $payment->late_pay_charge;
            return Reply::successData($payment);
        }else{
            return Reply::error('Payment not found.');
        }
    }


    /**
     * Pay the loan, Check for remaining amount and update the loan status
     *
     * @param \App\Models\Loan $loan_id
     * @param \App\Models\LoanPayment $payment_id
     * @return \Illuminate\Http\Response
     */
    function pay($loan_id, $payment_id, Request $request){
        $payment = \App\Models\LoanPayment::where('loan_id', $loan_id)->where('id', $payment_id)->with('loan')->first();

        if($payment && $payment->loan->user_id == Auth::user()->id){

            if($request->status == 'success'){
                $payment->status = 'paid';
                $payment->payment_done_date = Carbon::now()->format('Y-m-d');
                $payment->save();

                $log = new \App\Models\LoanLog();

                $log->loan_id = $payment->loan->id;
                $log->action_by = Auth::user()->id;
                $log->message = 'Payment of '.$payment->amount.' & Late Charge '. $payment->late_pay_charge.' was made.';
                $log->action = 'PAYMENT_SUCCESS';

                $log->save();

                $loan = $payment->loan;

                $loan->pending_terms -= 1;
                $loan->pending_amount -= $payment->amount;

                $loan->save();

                if($loan->pending_terms == 0){
                    $loan->status = 'completed';
                    $loan->save();
                }

                return Reply::success('Payment Success.');
            }

                $log = new \App\Models\LoanLog();

                $log->loan_id = $payment->loan->id;
                $log->action_by = Auth::user()->id;
                $log->message = 'Payment of '.$payment->amount.' & Late Charge '. $payment->late_pay_charge.' was Failed.';
                $log->action = 'PAYMENT_FAIL';

            return Reply::error('Payment Failed.');
        }else{
            return Reply::error('Payment not found.');
        }
    }
}
