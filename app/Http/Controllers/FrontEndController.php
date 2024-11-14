<?php

namespace App\Http\Controllers;

use App\Models\Cms;
use App\Models\Merchant;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FrontEndController extends Controller
{
    public function init(Request $request) {
        $validation = Validator::make($request->all(),[ 
            'fe_code' => 'required',
            'merchant_code' => 'required',
            'terminal_code' => 'required',
        ]);
    
        //validasi request 
        if($validation->fails()){
            //jika tidak sesuai maka return error invalid parameter
            return response()->json([
                'status' => false,
                'code' => '01',
                'message' => 'Invalid parameter',
                'data' => null                      
            ], 200);

        }

        //query get data merchant dan terminal
        $query = DB::table('merchants')
            ->join('terminals', 'merchants.merchant_id', '=', 'terminals.merchant_id')
            ->where('merchants.merchant_code', $request->merchant_code)
            ->where('terminals.terminal_code', $request->terminal_code)
            ->first();


        if (!$query) {
            
            return response()->json([
                'status' => false,
                'code' => '02',
                'message' => 'Invalid parameter',
                'data' => null 
            ], 200);
        }


        if ($query->is_active == true && $query->fe_code != $request->fe_code) {
            return response()->json([
                'status' => false,
                'code' => '03',
                'message' => 'Invalid FE Code ',
                'data' => null 
            ], 200);
        }

        if ($query->is_active == false) {
            Terminal::where('terminal_code', $request->terminal_code)->update([
                'is_active' => true,
                'fe_code' => $request->fe_code
            ]);
        }

        // $get_price = Cms::where('cms_code', '1')->first();
        $get_price = Cms::where('terminal_id', $query->terminal_id)->where('cms_name', 'price')->first();

        return response()->json([
            'status' => true,
            'code' => '00',
            'message' => 'Success',
            'data' => [
                'price' => $get_price->cms_value
            ]
        ], 200);
        
    }
}
