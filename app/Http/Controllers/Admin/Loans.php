<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helper\Reply;
use Carbon\Carbon;
use Auth;

class Loans
{
    /**
     * Get all the Loans data
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
     * Approve/Reject any loan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function update_loan(Request $request){
        $validated = $request->validate([
            'loan_id' =>  'required|exists:loans,id',
            'status' =>  'required|in:approved,rejected',
        ]);

        $loan = \App\Models\Loan::find($request->loan_id);

        if($loan->status == 'approved'){
            return Reply::error('Loan already approved.');
        }elseif($request->status == 'approved'){

            $amount = $loan->loan_amount;

            $loan_term = $loan->loan_term;

            $interest = 10;

            $total_amount = ($amount + ($amount * $interest / 100));

            $emi_amount = $total_amount / $loan_term;

            $loan->final_amount = $total_amount;
            $loan->interest = $interest;
            $loan->status = 'approved';
            $loan->pending_terms = $loan_term;
            $loan->pending_amount = $total_amount;

            $loan->save();

            for($i = 1; $i <= $loan_term; $i++){
                $emi = new \App\Models\LoanPayment();

                $emi->loan_id = $loan->id;
                $emi->amount = $emi_amount;
                $emi->payment_date = Carbon::now()->addDays($i * 7)->format('Y-m-d');
                $emi->status = 'pending';

                $emi->save();
            }

            $log = new \App\Models\LoanLog();

            $log->loan_id = $loan->id;
            $log->action_by = Auth::user()->id;
            $log->message = 'Loan Approved';
            $log->action = 'STATUS';

            $log->save();

            return Reply::success('Loan Updated Successfully.');
        }else{
            $loan->status = $request->status;

            $loan->save();

            $log = new \App\Models\LoanLog();

            $log->loan_id = $loan->id;
            $log->action_by = Auth::user()->id;
            $log->message = 'Loan '.$request->status;
            $log->action = 'STATUS';

            $log->save();

            return Reply::success('Loan Updated Successfully.');
        }
    }
}
