<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warning;
use Validator, Input, Redirect;

class WarningApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warnings = Warning::all();

        return response()->json([
            "status" => "success",
            "warnings" => $warnings,
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
            "loan_interval_id" => 'required|numeric|min:0',
            "level"=> 'required|numeric|between:0,6',
            "warning_issued_date" => 'required|date',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();

            $warning = new Warnings;

            $warning->loan_interval_id = $data['loan_interval_id'];
            $warning->level = $data['level'];
            $warning->warning_issued_date = $data['warning_issued_date'];

            $warning->save();

            return response()->json([
                "status" => "success",
                "warnings" => $warnings,
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
            $warnings = Warning::find($id);

            return response()->json([
                "status" => "success",
                "warnings" => $warnings,
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
            "loan_interval_id" => 'required|numeric|min:0',
            "level"=> 'required|numeric|between:0,6',
            "warning_issued_date" => 'required|date',
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

            $warning = Warning::find($id);
            $warning->loan_interval_id = $data['loan_interval_id'];
            $warning->level = $data['level'];
            $warning->warning_issued_date = $data['warning_issued_date'];
            $warning->created_at = $data['created_at'];
            $warning->updated_at = $data['updated_at'];

            $warning->save();

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

            $warning = Warning::find($id);
            $warning->delete();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }
}
