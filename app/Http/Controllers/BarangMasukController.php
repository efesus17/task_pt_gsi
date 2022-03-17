<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BarangMasukController extends Controller
{
    protected $user;

    public function __construct() 
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index() 
    {
        return BarangMasuk::get();
        // return BarangMasuk::leftJoin('barangs', 'barangs.id', 'barang_masuks.barang_id')
        //     ->select('barang_masuks.*', 'barangs.name as barang_name')
        //     ->get();
    }

    public function create() 
    {
        //
    }

    public function store(Request $request) 
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tanggal_masuk' => 'required|string',
            'nama_supplier' => 'required',
            'alamat' => 'required',
            'barang_id' => 'required',
            'qty' => 'required',
            'harga' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $BarangMasuk = BarangMasuk::create([
            'tanggal_masuk' => $request->tanggal_masuk,
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'barang_id' => $request->barang_id,
            'qty' => $request->qty,
            'harga' => $request->harga
        ]);
        $jml_qty_exits = Barang::where('id', $request->barang_id)->sum('quantity');
        $updateQty = Barang::where('id', $request->barang_id);
        $updateQty->update(array('quantity' => $jml_qty_exits + $request->qty));
        return response()->json([
            'success' => true,
            'message' => 'BarangMasuk created successfully',
            'data' => $BarangMasuk
        ], Response::HTTP_OK);
    }

    public function show($id) 
    {
        $BarangMasuk = BarangMasuk::find($id);
        if (!$BarangMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, BarangMasuk not found.'
            ], 400);
        }
        return $BarangMasuk;
    }

    public function edit(BarangMasuk $BarangMasuk) 
    {
        //
    }

    public function update(Request $request, BarangMasuk $BarangMasuk) 
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tanggal_masuk' => 'required|string',
            'nama_supplier' => 'required',
            'alamat' => 'required',
            'barang_id' => 'required',
            'qty' => 'required',
            'harga' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $BarangMasuk = $BarangMasuk->update([
            'tanggal_masuk' => $request->tanggal_masuk,
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'barang_id' => $request->barang_id,
            'qty' => $request->qty,
            'harga' => $request->harga
        ]);
        $jml_qty_exits = Barang::where('id', $request->barang_id)->sum('quantity');
        $updateQty = Barang::where('id', $request->barang_id);
        $updateQty->update(array('quantity' => $jml_qty_exits +  $request->qty));
        return response()->json([
            'success' => true,
            'message' => 'BarangMasuk updated successfully',
            'data' => $BarangMasuk
        ], Response::HTTP_OK);
    }

    public function destroy(BarangMasuk $BarangMasuk) 
    {
        $BarangMasuk->delete();
        return response()->json([
            'success' => true,
            'message' => 'BarangMasuk deleted successfully'
        ], Response::HTTP_OK);
    }

    public function laporanBarangMasuk(Request $request) 
    {
        $data = $request->only('tgl_awal', 'tgl_akhir');
        $validator = Validator::make($data, [
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $laporan =  BarangMasuk::leftJoin('barangs', 'barangs.id', 'barang_masuks.barang_id')
            ->select('barang_masuks.tanggal_masuk', 'barang_masuks.nama_supplier', 'barang_masuks.alamat', 'barangs.name as barang_name', 'barang_masuks.qty', 'barang_masuks.harga');
        if (!empty($request->tgl_awal) && !empty($request->tgl_akhir)) {
            $laporan = $laporan->where('tanggal_masuk', '>=', $request->tgl_awal)->where('tanggal_masuk', '<=', $request->tgl_akhir);
        }
        return $laporan->get();
    }
}
