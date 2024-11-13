<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    /**
     * Display a listing of the configs.
     */
    public function index()
    {
        $configs = Config::all();
        return response()->json($configs);
    }

    /**
     * Store a newly created config in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'terminal_id' => 'required|integer',
            'payment_type_id' => 'required|integer',
            'config_merchant_id' => 'required|string|max:100',
            'config_terminal_id' => 'required|string|max:100',
            'config_pos_id' => 'required|string|max:100',
            'config_user' => 'required|string|max:100',
            'config_password' => 'required|string|max:100',
            'created_by' => 'required|integer',
        ]);

        $config = Config::create($validatedData);
        return response()->json($config, 201);
    }

    /**
     * Display the specified config.
     */
    public function show($id)
    {
        $config = Config::find($id);

        if (!$config) {
            return response()->json(['message' => 'Config not found'], 404);
        }

        return response()->json($config);
    }

    /**
     * Update the specified config in storage.
     */
    public function update(Request $request, $id)
    {
        $config = Config::find($id);

        if (!$config) {
            return response()->json(['message' => 'Config not found'], 404);
        }

        $validatedData = $request->validate([
            'terminal_id' => 'sometimes|integer',
            'payment_type_id' => 'sometimes|integer',
            'config_merchant_id' => 'sometimes|string|max:100',
            'config_terminal_id' => 'sometimes|string|max:100',
            'config_pos_id' => 'sometimes|string|max:100',
            'config_user' => 'sometimes|string|max:100',
            'config_password' => 'sometimes|string|max:100',
            'updated_by' => 'required|integer',
        ]);

        $config->update($validatedData);
        return response()->json($config);
    }

    /**
     * Remove the specified config from storage.
     */
    public function destroy($id)
    {
        $config = Config::find($id);

        if (!$config) {
            return response()->json(['message' => 'Config not found'], 404);
        }

        $config->delete();
        return response()->json(['message' => 'Config deleted successfully']);
    }
}
