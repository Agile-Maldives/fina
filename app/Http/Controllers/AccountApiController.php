<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Loan;
use Validator, Input, Redirect;
use DB;
use Storage;

class AccountApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
        $accountsWithSumsMVR = DB::table('accounts')
                  ->select(
                      'accounts.id',
                      DB::raw('SUM(loan_amount) as sum_loan_amount'),
                      DB::raw('SUM(balance_due) as sum_balance_due')
                  )
                  ->leftJoin('loans', 'accounts.id', '=', 'loans.account_id')
                  ->groupBy('accounts.id');

        $accounts =  DB::table('accounts')
                  ->joinSub($accountsWithSums, 'accounts_with_sums', function ($join) {
                      $join->on('accounts.id', '=', 'accounts_with_sums.id');
                  })->get();
        */

        $accounts = DB::select(DB::raw("SELECT 
                                            *
                                        FROM
                                            accounts
                                                LEFT JOIN
                                            (SELECT 
                                                mvr.account_id,
                                                    mvr_loan_amount,
                                                    mvr_balance_due,
                                                    usd_loan_amount,
                                                    usd_balance_due
                                            FROM
                                                (SELECT 
                                                account_id,
                                                    SUM(loan_amount) AS mvr_loan_amount,
                                                    SUM(balance_due) AS mvr_balance_due
                                            FROM
                                                fina.loans
                                            WHERE
                                                currency = 'MVR'
                                            GROUP BY account_id) mvr
                                            LEFT JOIN (SELECT 
                                                account_id,
                                                    SUM(loan_amount) AS usd_loan_amount,
                                                    SUM(balance_due) AS usd_balance_due
                                            FROM
                                                fina.loans
                                            WHERE
                                                currency = 'USD'
                                            GROUP BY account_id) usd ON mvr.account_id = usd.account_id) loans ON loans.account_id = accounts.id"));

      $totalSums = DB::select(DB::raw("SELECT
                                            mvr_loan_amount,
                                            mvr_balance_due,
                                            usd_loan_amount,
                                           usd_balance_due
                                       FROM
                                        (SELECT
                                            SUM(loan_amount) AS mvr_loan_amount,
                                            SUM( balance_due) AS mvr_balance_due
                                        FROM
                                            fina.loans
                                        WHERE
                                            currency = 'MVR'
                                        ) mvr
                                        INNER JOIN (SELECT
                                            SUM(loan_amount) AS usd_loan_amount,
                                            SUM( balance_due) AS usd_balance_due
                                        FROM
                                            fina.loans
                                        WHERE
                                            currency = 'USD'
                                        ) usd limit 1"));

        return response()->json([
            "status" => "success",
            "accounts" => $accounts,
            "totals" =>$totalSums
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

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
            "type" => 'required|numeric|min:0',
            "account_name"=> 'required|regex:/^[0-9a-zA-Z\- ]+$/',
            "account_phone_no" => 'required|numeric|min:0',
            "account_id_card"=> 'required|regex:/^[0-9a-zA-Z]+$/|unique:accounts,account_id_card',
            "id_card_attachment" => 'required|max:1000|mimetypes:image/jpeg,image/png,application/pdf',
            "nation_id" => 'required|numeric|min:1'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();


            $idCardUploadedPath = $request->file('id_card_attachment')->store('id_cards');

            $randomString = $this->generateRandomString(10);

            $account = new Account;

            $account->type = $data['type'];
            $account->account_name = $data['account_name'];
            $account->account_id_card = $data['account_id_card'];
            $account->account_phone_no = $data['account_phone_no'];
            $account->nation_id = $data['nation_id'];
            $account->id_card_attachment = $idCardUploadedPath;
            $account->code = $data['account_id_card'] . $randomString;
            $account->save();

            return response()->json([
                "status" => "success",
                "account" => $account,
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
            $account = Account::find($id);

            return response()->json([
                "status" => "success",
                "account" => $account,
            ],200);
        }
    }

    public function showByCode($code)
    {
        $request['code'] = $code;
        $validator = Validator::make($request, [
            "code" => 'required|alpha_num'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $account = Account::where('code',$code)->first();
            $loans = Loan::where('account_id',$account->id)->get();

            $balanceDue=0;
            $totalIssued=0;

            foreach($loans as $loan){
                $balanceDue+=$loan->balance_due;
                $totalIssued+=$loan->loan_amount;
            }

	    $data=[];
	    $data["totalDue"]=$balanceDue;
	    $data["totalIssued"]=$totalIssued;
	    $data['loans']=$loans;
	    $data['account']=$account;

	    return view('info', $data);
            /*
            return response()->json([
                "status" => "success",
                "loans" => $loans,
            ],200);
            */
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
            "id" => 'required|numeric|min:0',
            "account_id" => 'required|numeric|min:0',
            //            "type" => 'required|numeric|min:0',
            "account_name"=> 'required|regex:/^[0-9a-zA-Z\- ]+$/',
            "account_phone_no" => 'required|numeric|min:0',
            "account_id_card"=> 'required|regex:/^[0-9a-zA-Z]+$/',
            "id_card_attachment"=> 'nullable|image',
            //            "nation_id" => 'required|numeric|min:1'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $idCardAttachmentPath = null;

            $account = Account::find($id);
            if($data['id_card_attachment']) {
                $idCardAttachmentPath = $request->file('id_card_attachment')->store('id_cards');
            }

            // $account->type = $data['type'];
            $account->account_name = $data['account_name'];
            $account->account_id_card = $data['account_id_card'];
            $account->account_phone_no = $data['account_phone_no'];
            if($idCardAttachmentPath){
                $account->id_card_attachment = $data['id_card_attachment'];
            }
            //$account->nation_id = $data['nation_id'];

            $account->where('id','=',$data['account_id']);
            $account->save();

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
            $account = Account::find($id);
            $account->delete();

            return response()->json([
                "status" => "success"
            ],200);
        }
    }

    public function downloadIdCard($idCardAttachmentPath){
        return Storage::download("id_cards/".$idCardAttachmentPath);
    }

}
