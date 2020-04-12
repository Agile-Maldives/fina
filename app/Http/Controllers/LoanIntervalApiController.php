<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanInterval;
use App\Loan;
use App\GeneralSetting;
use Validator, Input, Redirect, Response;
use DB;
use PDF;

class LoanIntervalApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loanIntervals = LoanInterval::all();

        return response()->json([
            "status" => "success",
            "loanIntervals" => $loanIntervals,
        ],200);
    }


    public function accountStatement($accountId) {
        $request['id'] = $accountId;
        $validator = Validator::make($request, [
            "id" => 'required|numeric|min:0'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {

            $statement = DB::table('loan_intervals')
                       ->select('loan_intervals.id as key',
                                'loan_intervals.*',
                                'loans.*',
                                'accounts.*')
                       ->leftJoin('loans','loan_intervals.loan_id','=','loans.id')
                       ->leftJoin('accounts','loans.account_id','=','accounts.id')
                       ->where('account_id','=',$accountId)
                       ->get();

            return response()->json([
                "status" => "success",
                "account_statement" => $statement,
            ],200);
        }
    }

    public function accountStatementPdf($accountId){
        $statement = DB::table('loan_intervals')
                   ->leftJoin('loans','loan_intervals.loan_id','=','loans.id')
                   ->leftJoin('accounts','loans.account_id','=','accounts.id')
                   ->where('account_id','=',$accountId)
                   ->get();
        $generalSettings = GeneralSetting::first();

        $data = [
            'title' => 'Account Statement',
            'statement' => $statement,
            'generalSettings' => $generalSettings
        ];
        $pdf = PDF::loadView('account_statement_for_account', $data);  
        return $pdf->download('account_statement_for_account.pdf');
    }

    public function accountStatementCsv($accountId){
        $statement = DB::table('loan_intervals')
                   ->leftJoin('loans','loan_intervals.loan_id','=','loans.id')
                   ->leftJoin('accounts','loans.account_id','=','accounts.id')
                   ->where('account_id','=',$accountId)
                   ->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Account Name', 'Date Borrowing Start', 'Borrowing ID', 'Agreement ID', 'Currency', 'Issued Amount', 'Due Date', 'Due Amount', 'Paid');

        $callback = function() use ($statement, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($statement as $entry) {
                fputcsv($file, array(
                    $entry->account_name,
                    date('m-d-Y',strtotime($entry->date_loan_start)),
                    $entry->id,
                    $entry->agreement_no,
                    $entry->currency,
                    number_format($entry->loan_amount,2),
                    date('m-d-Y',strtotime($entry->due_date_loan_completion)),
                    number_format($entry->due_amount,2),
                    ($entry->loan_paid==0)?"NO":"YES"
                   ));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }




    public function allAccountsStatement(){
        $statement = DB::table('loan_intervals')
                   ->leftJoin('loans','loan_intervals.loan_id','=','loans.id')
                   ->leftJoin('accounts','accounts.id','=','loans.account_id')
                   ->get();

        return response()->json([
            "status" => "success",
            "all_accounts_statement" => $statement,
        ],200);
    }

    public function allAccountsStatementPdf(){
        $statement = DB::table('loan_intervals')
                   ->leftJoin('loans','loan_intervals.loan_id','=','loans.id')
                   ->leftJoin('accounts','loans.account_id','=','accounts.id')
                   ->get();
        $generalSettings = GeneralSetting::first();

        $data = [
            'title' => 'Account Statement',
            'statement' => $statement,
            'generalSettings' => $generalSettings
        ];
        $pdf = PDF::loadView('all_accounts_statement', $data);
        return $pdf->download('account_statement_for_account.pdf');
    }

    public function allAccountsStatementCsv(){
        $statement = DB::table('loan_intervals')
                   ->leftJoin('loans','loan_intervals.loan_id','=','loans.id')
                   ->leftJoin('accounts','loans.account_id','=','accounts.id')
                   ->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = array('Account Name', 'Date Borrowing Start', 'Borrowing ID', 'Agreement ID', 'Currency', 'Issued Amount', 'Due Date', 'Due Amount', 'Paid');

        $callback = function() use ($statement, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($statement as $entry) {
                fputcsv($file, array(
                    $entry->account_name,
                    date('m-d-Y',strtotime($entry->date_loan_start)),
                    $entry->id,
                    $entry->agreement_no,
                    $entry->currency,
                    number_format($entry->loan_amount,2),
                    date('m-d-Y',strtotime($entry->due_date_loan_completion)),
                    number_format($entry->due_amount,2),
                    ($entry->loan_paid==0)?"NO":"YES"
                ));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLoanIntervalsByLoanId($loanId)
    {
        $loanIntervals = LoanInterval::where('loan_id',$loanId)->get();

        return response()->json([
            "status" => "success",
            "loanIntervals" => $loanIntervals,
        ],200);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "loan_id" => 'required|numeric|min:0',
            "due_date"=> 'required|date',
            "due_amount" => 'require|between:0, 99999999999',
            "paid_status"=> 'required|boolean',
            "created_at"=> 'required|date',
            "updated_at" => 'required|date',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $loanInterval = new LoanInterval;

            $loanInterval->loan_id = $data['loan_id'];
            $loanInterval->due_date = $data['due_date'];
            $loanInterval->due_amount = $data['due_amount'];
            $loanInterval->paid_status = $data['paid_status'];
            $loanInterval->created_at = $data['created_at'];
            $loanInterval->updated_at = $data['updated_at'];

            $loanInterval->save();

            return response()->json([
                "status" => "success",
                "loanInterval" => $loanInterval,
            ],200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $request['id'] = $id;
        $validator = Validator::make($request, [
            "id" => 'required|numeric|min:0'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $loanInterval = LoanInterval::find($id);

            return response()->json([
                "status" => "success",
                "loan interval" => $loanInterval,
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $requestData['id'] = $id;

        $validator = Validator::make($requestData, [
            "loan_id" => 'required|numeric|min:0',
            "due_date"=> 'required|date',
            "due_amount" => 'require|between:0, 99999999999',
            "paid_status"=> 'required|boolean',
            "created_at"=> 'required|date',
            "updated_at" => 'required|date',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $loanInterval = LoanInterval::find($id);
            $loanInterval->loan_id = $data['loan_id'];
            $loanInterval->due_date = $data['due_date'];
            $loanInterval->due_amount = $data['due_amount'];
            $loanInterval->paid_status = $data['paid_status'];
            $loanInterval->created_at = $data['created_at'];
            $loanInterval->updated_at = $data['updated_at'];

            $loanInterval->save();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payInstallment(Request $request, $id)
    {
        $requestData = $request->all();
        $requestData['id'] = $id;

        $validator = Validator::make($requestData, [
            "id" => 'required|numeric|min:0|exists:loan_intervals,id',
            "paid_status"=> 'required|boolean',
            "due_amount"=> 'sometimes|required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $loanInterval = LoanInterval::find($id);
            $loanInterval->paid_status = $data['paid_status'];

            if(isset($data['due_amount'])){
                $loanInterval->due_amount = $data['due_amount'];
            }

            $loanInterval->save();

            //get loan id and dueAmount
            $loanIntervalObj = LoanInterval::where('id', $id)->first();
            $loanId = $loanIntervalObj->loan_id;
            $dueAmount = $loanIntervalObj->due_amount;

            $loan = Loan::find($loanId);

            if(isset($data['due_amount'])){
                $dueAmount = $data['due_amount'];
            }

            if($data['paid_status']==0){
                //echo "loanintervalid:$id incrementing on loanid: $loanId  ".$dueAmount;
                $loan->increment('balance_due',$dueAmount);
                /*
                //get previous payment date
                $previous = LoanInterval::where('id', '<', $id)
                      ->where('loan_id','=',$loanId)
                      ->orderBy('id','desc')->first();
                $dueDatePreviousPayment = $previous->due_date;
                $loan->due_date_next_payment = $dueDatePreviousPayment;
                */
            }
            else {
                //echo "loanintervalid:$id decrement on loanid: $loanId  ".$dueAmount;
                $loan->decrement('balance_due',$dueAmount);
                /*
                //get next payment date
                $next = LoanInterval::where('id', '>', $id)
                      ->where('loan_id','=',$loanId)
                      ->orderBy('id')->first();
                $dueDateNextPayment = $next->due_date;
                $loan->due_date_next_payment = $dueDateNextPayment;
                */
            }
            $loan->save();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $requestData['id'] = $id;

        $validator = Validator::make($requestData, [
            "id" => 'required|numeric|min:0'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {

            $loanInterval = LoanInterval::find($id);
            $flight->delete();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }
}
