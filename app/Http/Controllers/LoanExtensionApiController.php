<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanExtension;
use Validator, Input, Redirect;

class LoanExtensionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loanExtensions = LoanExtension::all();

        return response()->json([
            "status" => "success",
            "loan_extensions" => $loanExtensions,
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
            "extended_date"=> 'required|date'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $loanExtension = new LoanExtension;

            $loanExtension->account_id = $data['account_id'];
            $loanExtension->loan_id = $data['loan_id'];
            $loanExtension->loan_interval_id = $data['loan_interval_id'];
            $loanExtension->created_at = $data['created_at'];
            $loanExtension->updated_at = $data['updated_at'];

            $loanExtension->save();

            return response()->json([
                "status" => "success",
                "loan payment" => $loanExtension,
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
            $loanExtension = LoanExtension::find($id);

            return response()->json([
                "status" => "success",
                "loan payment" => $loanExtension,
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
            "extended_date"=> 'required|date',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $loanExtension = new LoanExtension;

            $loanExtension->account_id = $data['account_id'];
            $loanExtension->loan_id = $data['loan_id'];
            $loanExtension->loan_interval_id = $data['loan_interval_id'];
            $loanExtension->created_at = $data['created_at'];
            $loanExtension->updated_at = $data['updated_at'];

            $loanExtension->save();

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

            $loanExtension = LoanExtension::find($id);
            $loanExtension->delete();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }
}
