<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator, Input, Redirect;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        $users = User::all();

        return response()->json([
            "status" => "success",
            "users" => $users,
        ],200);
    }

    /*
    public function createUser() {
        $validator = Validator::make($request->all(), [
            "type" => 'required|numeric|min:0',
            "account_name"=> 'required|regex:/^[0-9a-zA-Z\-]+$/',
            "account_phone_no" => 'required|numeric|min:0',
            "account_id_card"=> 'required|regex:/^[0-9a-zA-Z]+$/',
            "id_card_attachment"=> 'nullable|max:1000|mimes:jpeg,png',
            "nation_id" => 'required|numeric|min:1'
        ]);

        return Validator::make($data, [

       'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

            $account = new Account;

            $account->type = $data['type'];
            $account->account_name = $data['account_name'];
            $account->account_id_card = $data['account_id_card'];
            $account->account_phone_no = $data['account_phone_no'];
            $account->nation_id = $data['nation_id'];
            $account->id_card_attachment = $idCardUploadedPath;
            $account->save();

            return response()->json([
                "status" => "success",
                "account" => $account,
            ],200);
        }
    }
    */

}
