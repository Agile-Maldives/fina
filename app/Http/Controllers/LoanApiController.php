<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\LoanInterval;
use App\GeneralSetting;
use Validator, Input, Redirect, Response;
use PDF;
use Storage;

class LoanApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loan::all();

        return response()->json([
            "status" => "success",
            "loans" => $loans,
        ],200);
    }

    public function loansIssuedReport(){
        $loans = Loan::with('account')->get();

        return response()->json([
            "status" => "success",
            "loans" => $loans,
        ],200);
    }

    public function loansIssuedReportPdf(){
        $loans = Loan::with('account')->get();
        $generalSettings = GeneralSetting::first();

        $data = [
            'title' => 'Loans Issued',
            'loans' => $loans,
            'generalSettings' => $generalSettings
            ];
        $pdf = PDF::loadView('loans_issued_pdf', $data);  
        return $pdf->download('loans-issued-report.pdf');
    }

    public function loansIssuedReportCsv(){
        $loans = Loan::with('account')->get();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Account Name', 'ID Card', 'Borrowing ID', 'Agreement ID', 'Currency', 'Date Loan Start', 'Issued Amount', 'Payment Interval Type');

        $callback = function() use ($loans, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($loans as $loan) {
                fputcsv($file, array(
                    $loan->account->account_name,
                    $loan->account->account_id_card,
                    $loan->id,
                    $loan->agreement_no,
                    $loan->currency,
                    date('m-d-Y H:i',strtotime($loan->date_loan_start)),
                    number_format($loan->loan_amount,2),
                    ($loan->payment_interval_type==0)?"Daywise":"Monthwise"
                   ));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public function duesPerLoanForAccount($accountId){
        $loans = Loan::with('account')
               ->where('account_id', $accountId)
               ->get();

        return response()->json([
            "status" => "success",
            "loans" => $loans,
        ],200);
    }

    public function duesPerLoanForAccountPdf($accountId){
        $loans = Loan::with('account')
               ->where('account_id', $accountId)
               ->get();
        $generalSettings = GeneralSetting::first();

        $data = [
            'title' => 'Dues Per Loan For Account',
            'loans' => $loans,
            'generalSettings' => $generalSettings
        ];
        $pdf = PDF::loadView('dues_per_loan_for_account', $data);  
        return $pdf->download('dues_per_loan_for_account.pdf');
    }

    public function duesPerLoanForAccountCsv($accountId){
        $loans = Loan::with('account')
               ->where('account_id', $accountId)
               ->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Account Name', 'ID Card', 'Borrowing id', 'Agreement id', 'Currency', 'Date Borrowing Start', 'Issued amount', 'Payment Interval Due Date', 'Payment Interval Due amount');

        $callback = function() use ($loans, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($loans as $loan) {
                fputcsv($file, array(
                    $loan->account->account_name,
                    $loan->account->account_id_card,
                    $loan->id,
                    $loan->agreement_no,
                    $loan->currency,
                    date('m-d-Y',strtotime($loan->date_loan_start)),
                    number_format($loan->loan_amount,2),
                    date('m-d-Y',strtotime($loan->due_date_loan_completion)),
                    number_format($loan->balance_due,2)
                   ));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }



    public function duesPerLoanForAllAccounts(){
        $loans = Loan::with('account')
               ->get();

        return response()->json([
            "status" => "success",
            "loans" => $loans,
        ],200);

    }

    public function duesPerLoanForAllAccountsPdf(){
        $loans = Loan::with('account')
               ->get();
        $generalSettings = GeneralSetting::first();
        $data = [
            'title' => 'Dues Per Loan For All Accounts',
            'loans' => $loans,
            'generalSettings' => $generalSettings
        ];
        $pdf = PDF::loadView('dues_per_loan_for_all_accounts', $data);  
        return $pdf->download('dues_per_loan_for_all_accounts.pdf');
    }

    public function duesPerLoanForAllAccountsCsv(){
        $loans = Loan::with('account')
               ->get();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Account Name', 'ID Card', 'Borrowing ID', 'Agreement ID', 'Currency', 'Date Borrowing Start', 'Issued amount', 'Payment Interval Due Date', 'Payment Interval Due amount');

        $callback = function() use ($loans, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($loans as $loan) {
                fputcsv($file, array(
                    $loan->account->account_name,
                    $loan->account->account_id_card,
                    $loan->id,
                    $loan->agreement_no,
                    $loan->currency,
                    date('m-d-Y',strtotime($loan->date_loan_start)),
                    number_format($loan->loan_amount,2),
                    date('m-d-Y',strtotime($loan->due_date_loan_completion)),
                    number_format($loan->balance_due,2)
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
    public function getLoansByAccountId($accountId)
    {
        $loans= Loan::where('account_id',$accountId)->get();

        return response()->json([
            "status" => "success",
            "loans" => $loans,
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
           "account_id" => 'required|numeric|min:0',
           "payment_interval_type" => 'required|numeric|min:0',
           "loan_duration_days" =>  'required|numeric|min:0',
           "date_loan_start" => 'required|date',
           "due_date_loan_completion" =>  'required|date',
           //"due_date_loan_completion_extended" =>  'required|date',
           //"due_date_next_payment" =>  'required|date',
           "loan_amount" => 'required|between:0, 99999999999',
           "balance_due" => 'required|between:0, 99999999999',
           "interest_rate_percent" => 'nullable|numeric|between:0,100',
           "interest_rate_value" => 'nullable|between:0, 99999999999',
           "currency" => 'required|regex:/^[a-zA-Z]+$/',
           //"loan_paid" => 'required|numeric|between:0,1',
           //"payment_per_interval" => 'required|between:0, 99999999999',
           //"payment_interval_days" => 'required|numeric|between:1,31',
           "agreement_attachment" => 'required|max:1000|mimetypes:image/jpeg,image/png,application/pdf',
           "guardian_name" => 'required|regex:/^[a-zA-Z ]+$/',
           "guardian_phone_no" => 'required|numeric|min:0',
           "guardian_address" => 'required|regex:/^[0-9a-zA-Z\-\. ]+$/',
           "guardian_id_card" => 'required|regex:/^[0-9a-zA-Z]+$/',
           "agreement_no" => 'required'
        ]);

        $validator->sometimes('payment_interval_days', 'required|numeric|min:1', function ($input) {
            return $input->payment_interval_type == 0;
        });

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();


            if(!isset($data['interest_rate_percent'])){
                $data['interest_rate_percent']=0;
            }
            if(!isset($data['interest_rate_value'])){
                $data['interest_rate_value']=0;
            }

            $paymentIntervalDays = $data['payment_interval_type']==0?$data['payment_interval_days']:NULL;


            $aggreementAttachmentPath = $request->file('agreement_attachment')->store('aggreements');
            //Populate loan intervals
            $intervalDates = $this->getDatePeriodDatesWithInteval(
                $data['date_loan_start'],
                $data['due_date_loan_completion'],
                $data['payment_interval_type'],
                $paymentIntervalDays
            );

            echo "payment interval type:" . $data['payment_interval_type'];
            //Payment per interval = loan amount * (10/100) * number-of-periods
	    $paymentAmountPerInterval=0;
	    if($data['interest_rate_percent']==0){
		    $paymentAmountPerInterval= $data['loan_amount']/count($intervalDates);
	    }
	    else {
		    $paymentAmountPerInterval= $data['loan_amount'] * ($data['interest_rate_percent']/100) * count($intervalDates);
	    }

            $loan = new Loan;
            $loan->account_id = $data['account_id'];
            $loan->loan_duration_days = $data['loan_duration_days'];
            $loan->date_loan_start = $data['date_loan_start'];
            $loan->due_date_loan_completion = $data['due_date_loan_completion'];
            //$loan->due_date_loan_completion_extended = $data['due_date_loan_completion_extended'];
            $loan->due_date_next_payment = $data['date_loan_start'];
            $loan->loan_amount = $data['loan_amount'];
            $loan->balance_due = $data['loan_amount'];
            $loan->interest_rate_percent = $data['interest_rate_percent'];
            $loan->Interest_rate_value = $data['loan_amount'] * ($data['interest_rate_percent']/100);
            //$loan->loan_paid = $data['loan_paid'];
            $loan->payment_per_interval = $paymentAmountPerInterval;
            $loan->payment_interval_type = $data['payment_interval_type'];
            $loan->payment_interval_days = $data['payment_interval_type']==0?$data['payment_interval_days']:NULL;
            $loan->currency = $data['currency'];
            $loan->agreement_attachment = $aggreementAttachmentPath;
            $loan->guardian_name = $data['guardian_name'];
            $loan->guardian_phone_no = $data['guardian_phone_no'];
            $loan->guardian_address = $data['guardian_address'];
            $loan->guardian_id_card = $data['guardian_id_card'];
            $loan->agreement_no = $data['agreement_no'];

            $loan->save();


            foreach($intervalDates as $date){
                $loanInterval = new LoanInterval;

                $loanInterval->loan_id = $loan->id;
                $loanInterval->due_date = $date;
                $loanInterval->due_amount = $paymentAmountPerInterval;
                $loanInterval->paid_status = 0;
                $loanInterval->save();
            }

            return response()->json([
                "status" => "success",
                "loan" => $loan,
            ],200);
        }
    }

    /**
     * Return a list of dates between two dates separated by given interval days
     *
     * @param  string $begin
     * @param  string $end
     * @param  int $interval
     * @return \Illuminate\Http\Response
     */

    public function getDatePeriodDatesWithInteval($begin, $end, $paymentIntervalType, $paymentIntervalDays=0){

        $dates = [];

        $beginDateObj = new \DateTime( $begin );
        $endDateObj = new \DateTime( $end );

        $interval=null;
        if($paymentIntervalType==0){
            $interval = new \DateInterval("P{$paymentIntervalDays}D");
        } else {
            $interval = new \DateInterval("P1M");
        }

        $daterange = new \DatePeriod($beginDateObj, $interval ,$endDateObj);

        foreach($daterange as $date){
            $dates[] = $date->format("Y-m-d");
        }

        return $dates;
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
            $loan = Loan::find($id);

            return response()->json([
                "status" => "success",
                "loan" => $loan,
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
           "account_id" => 'required|numeric|min:0',
           //"loan_duration_days" => 'required|numeric|min:0',
           //"date_loan_start" => 'required|date',
           //"due_date_loan_completion" =>  'required|date',
           "due_date_loan_completion_extended" =>  'nullable|date',
           //"due_date_next_payment" =>  'nullable|date',
           //"loan_amount" => 'required|between:0, 99999999999',
           "balance_due" => 'required|between:0, 99999999999',
           //"interest_rate_percent" => 'required|numeric|between:0,100',
           //"interest_rate_value" => 'required|between:0, 99999999999',
           "loan_paid" => 'required|numeric|between:0,1',
           //"payment_per_interval" => 'nullable|between:0, 99999999999',
           //"payment_interval_type" => 'required|numeric|min:0',
           "agreement_attachment" => 'nullable|image',
           "guardian_name" => 'required|regex:/^[a-zA-Z ]+$/',
           "guardian_phone_no" => 'required|numeric|min:0',
           "guardian_address" => 'required|regex:/^[0-9a-zA-Z\-]+$/',
           "guardian_id_card" => 'required|regex:/^[0-9a-zA-Z]+$/',
           "agreement_no" => 'required'
        ]);
        /*
        $validator->sometimes('payment_interval_days', 'required|numeric|min:1', function ($input) {
            return $input->payment_interval_type == 0;
        });
        */



        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $loan = Loan::find($id);

            //    $paymentIntervalDays = $data['payment_interval_type']==0?$data['payment_interval_days']:NULL;
            $aggreementAttachmentPath = null;
            if($data['aggreement_attachment']) {
                $aggreementAttachmentPath = $request->file('aggreement_attachment')->store('aggreements');
            }

            //$loan->account_id = $data['account_id'];
            // $loan->loan_duration_days = $data['loan_duration_days'];
            //$loan->date_loan_start = $data['date_loan_start'];
            $loan->due_date_loan_completion_extended = $data['due_date_loan_completion_extended']; //
            //$loan->due_date_next_payment = $data['due_date_next_payment'];
            //$loan->loan_amount = $data['loan_amount'];
            $loan->balance_due = $data['balance_due'];
            //$loan->interest_rate_percent = $data['interest_rate_percent'];
            //$loan->Interest_rate_value = $data['Interest_rate_value'];
            $loan->loan_paid = $data['loan_paid'];
            //$loan->payment_per_interval = $data['payment_per_interval'];
            //$loan->payment_interval_type = $data['payment_interval_type'];
            //$loan->payment_interval_days = $data['payment_interval_days'];
            if($aggreementAttachmentPath){
                $loan->agreement_attachment = $aggreementAttachmentPath;
            }
            $loan->guardian_name = $data['guardian_name'];
            $loan->guardian_phone_no = $data['guardian_phone_no'];
            $loan->guardian_address = $data['guardian_address'];
            $loan->guardian_id_card = $data['guardian_id_card'];
            $loan->agreement_no =$data['agreement_no'];
            $loan->where('id','=',$data['loan_id']);


            if($data['loan_paid']=='1'){
                $loan->due_date_loan_completion = date('Y-m-d');
            }

            $loan->save();

            if($data['loan_paid']=='1'){
                LoanInterval::where('loan_id',$data['loan_id'])
                    ->update(['paid_status'=>1]);
            }

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
            $loan = Loan::find($id);
            $loan->delete();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }

    public function downloadAggreement($aggreementAttachmentPath){
        return Storage::download("aggreements/".$aggreementAttachmentPath);
    }
}
