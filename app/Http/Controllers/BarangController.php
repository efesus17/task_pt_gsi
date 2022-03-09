<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }


    public function index() 
    {
        return Barang::get();
        // return Barang::leftJoin('category_barangs', 'category_barangs.id', 'barangs.category_id')
        //     ->select('barangs.*', 'category_barangs.name as category_name')
        //     ->get();
    }
    
    public function create()
    {
        //
    }
    
    public function store(Request $request) 
    {
        $data = $request->only('name', 'category_id', 'quantity');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'category_id' => 'required',
            'quantity' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $Barang = $this->user->Barangs()->create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'quantity' => $request->quantity
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Barang created successfully',
            'data' => $Barang
        ], Response::HTTP_OK);
    }   
    
    public function show($id) 
    {
        $Barang = $this->user->Barangs()->find($id);
        if (!$Barang) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Barang not found.'
            ], 400);
        }
        return $Barang;
    }

    public function edit(Barang $Barang) 
    {
        //
    }

    public function update(Request $request, Barang $Barang) 
    {
        $data = $request->only('name', 'category_id', 'price', 'quantity');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'category_id' => 'required',
            'quantity' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $Barang = $Barang->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'quantity' => $request->quantity
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Barang updated successfully',
            'data' => $Barang
        ], Response::HTTP_OK);
    }

    public function destroy(Barang $Barang)
    {
        $Barang->delete();
        return response()->json([
            'success' => true,
            'message' => 'Barang deleted successfully'
        ], Response::HTTP_OK);
    }

    public function laporanStok()
    {
        return Barang::get();
        // return Barang::leftJoin('category_barangs', 'category_barangs.id', 'barangs.category_id')
        //     ->select('barangs.name', 'category_barangs.name as category_name', 'barangs.quantity as stok')
        //     ->get();
    }
}
