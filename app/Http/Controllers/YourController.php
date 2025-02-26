<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YourController extends Controller
{
    public function store(Request $request) {
        $data = $request->all();
        $jsonData = json_encode($data);
        file_put_contents(storage_path('data.json'), $jsonData);
    }

    public function show() {
        $data = json_decode(file_get_contents(storage_path('data.json')), true);
        return response()->json($data);
    }
}
