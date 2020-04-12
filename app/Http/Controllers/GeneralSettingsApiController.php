<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GeneralSetting;
use Validator, Input, Redirect;
use DB;
use Storage;

class GeneralSettingsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $generalSettings = GeneralSetting::first();

        return response()->json([
            "status" => "success",
            "generalSettings" => $generalSettings,
        ],200);
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
    public function update(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            "title"=> 'required|regex:/^[0-9a-zA-Z\- ]+$/',
            "id_card_attachment"=> 'nullable|mimetypes:image/png',
            "company_name"=> 'required|regex:/^[0-9a-zA-Z\- \.]+$/',
            "company_address"=> 'required|regex:/^[0-9a-zA-Z\- ,]+$/',
            "company_phone_no"=> 'required|numeric',
            "company_hotline_no"=> 'required|numeric',
            "company_fax_no"=> 'required|numeric',
            "company_email"=> 'required|regex:/^.+@.+$/i',

        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $data=$request->all();
            var_dump($data);

            $logoAttachmentPath = null;

            $generalSettings = GeneralSetting::find(1);
            var_dump($generalSettings);

            if($data['logo_attachment']) {
                $logoAttachmentPath = $request->file('logo_attachment')
                                              ->storeAs('logos', 'logo.png', ['disk' => 'public']);
            }

            $generalSettings->title = $data['title'];
            $generalSettings->company_name = $data['company_name'];
            $generalSettings->company_address = $data['company_address'];
            $generalSettings->company_phone_no = $data['company_phone_no'];
            $generalSettings->company_hotline_no = $data['company_hotline_no'];
            $generalSettings->company_fax_no = $data['company_fax_no'];
            $generalSettings->company_email = $data['company_email'];
            if($logoAttachmentPath){
                $generalSettings->logo_attachment = $logoAttachmentPath;
            }

            $generalSettings->save();

            return response()->json([
                "status" => "success",
            ],200);
        }
    }

    public function downloadLogo($logoAttachmentPath){
        return Storage::download("logos/".$logoAttachmentPath);
    }

}
