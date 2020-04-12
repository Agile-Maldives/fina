<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanPayment;
use Validator, Input, Redirect;

class LoanPaymentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loanPayments = LoanPayment::all();

        return response()->json([
            "status" => "success",
            "loan payments" => $loanPayments,
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
            "account_id"=> 'required|numeric|min:0',
            "loan_id" => 'required|numeric|min:0',
            "loan_interval_id"=> 'required|numeric|min:0',
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

            $loanPayment = new LoanPayment;

            $loanPayment->account_id = $data['account_id'];
            $loanPayment->loan_id = $data['loan_id'];
            $loanPayment->loan_interval_id = $data['loan_interval_id'];
            $loanPayment->created_at = $data['created_at'];
            $loanPayment->updated_at = $data['updated_at'];

            $loanPayment->save();

            return response()->json([
                "status" => "success",
                "loan payment" => $loanPayment,
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
            $loanPayment = LoanPayment::find($id);

            return response()->json([
                "status" => "success",
                "loan payment" => $loanPayment,
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
            "account_id"=> 'required|numeric|min:0',
            "loan_id" => 'required|numeric|min:0',
            "loan_interval_id"=> 'required|numeric|min:0',
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

            $loanPayment = new LoanPayment;

            $loanPayment->account_id = $data['account_id'];
            $loanPayment->loan_id = $data['loan_id'];
            $loanPayment->loan_interval_id = $data['loan_interval_id'];
            $loanPayment->created_at = $data['created_at'];
            $loanPayment->updated_at = $data['updated_at'];

            $loanPayment->save();

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

            $loanPayment = LoanPayment::find($id);
            $loanPayment->delete();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }
}
