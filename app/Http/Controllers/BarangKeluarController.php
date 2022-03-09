<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Barang;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BarangKeluarController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index() 
    {
        return BarangKeluar::get();
        // return BarangKeluar::leftJoin('barangs', 'barangs.id', 'barang_keluars.barang_id')
        //     ->select('barang_keluars.*', 'barangs.name as barang_name')
        //     ->get();
    }

    public function create() 
    {
        //
    }

    public function store(Request $request) 
    {
        $data = $request->only('tanggal_keluar', 'nama_customer', 'alamat', 'barang_id', 'qty', 'harga');
        $validator = Validator::make($data, [
            'tanggal_keluar' => 'required|string',
            'nama_customer' => 'required',
            'alamat' => 'required',
            'barang_id' => 'required',
            'qty' => 'required',
            'harga' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $BarangKeluar = BarangKeluar::create([
            'tanggal_keluar' => $request->tanggal_keluar,
            'nama_customer' => $request->nama_customer,
            'alamat' => $request->alamat,
            'barang_id' => $request->barang_id,
            'qty' => $request->qty,
            'harga' => $request->harga
        ]);

        $jml_qty_exits = Barang::where('id', $request->barang_id)->sum('quantity');

        $updateQty = Barang::where('id', $request->barang_id);
        $updateQty->update(array('quantity' => $jml_qty_exits -  $request->qty));
        return response()->json([
            'success' => true,
            'message' => 'BarangKeluar created successfully',
            'data' => $BarangKeluar
        ], Response::HTTP_OK);
    }

    public function show($id) 
    {
        $BarangKeluar = BarangKeluar::find($id);
        if (!$BarangKeluar) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, BarangKeluar not found.'
            ], 400);
        }
        return $BarangKeluar;
    }

    public function edit(BarangKeluar $BarangKeluar) 
    {
        //
    }

    public function update(Request $request, BarangKeluar $BarangKeluar) 
    {
        $data = $request->only('tanggal_keluar', 'nama_customer', 'alamat', 'barang_id', 'qty', 'harga');
        $validator = Validator::make($data, [
            'tanggal_keluar' => 'required|string',
            'nama_customer' => 'required',
            'alamat' => 'required',
            'barang_id' => 'required',
            'qty' => 'required',
            'harga' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $BarangKeluar = $BarangKeluar->update([
            'tanggal_keluar' => $request->tanggal_keluar,
            'nama_customer' => $request->nama_customer,
            'alamat' => $request->alamat,
            'barang_id' => $request->barang_id,
            'qty' => $request->qty,
            'harga' => $request->harga
        ]);
        $jml_qty_exits = Barang::where('id', $request->barang_id)->sum('quantity');
        $updateQty = Barang::where('id', $request->barang_id);
        $updateQty->update(array('quantity' => $jml_qty_exits -  $request->qty));
        return response()->json([
            'success' => true,
            'message' => 'BarangKeluar updated successfully',
            'data' => $BarangKeluar
        ], Response::HTTP_OK);
    }

    public function destroy(BarangKeluar $BarangKeluar) 
    {
        $BarangKeluar->delete();
        return response()->json([
            'success' => true,
            'message' => 'BarangKeluar deleted successfully'
        ], Response::HTTP_OK);
    }

    public function laporanBarangKeluar(Request $request) 
    {
        $data = $request->only('tgl_awal', 'tgl_akhir');
        $validator = Validator::make($data, [
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $laporan =  BarangKeluar::leftJoin('barangs', 'barangs.id', 'barang_keluars.barang_id')
            ->select('barang_keluars.tanggal_keluar', 'barang_keluars.nama_customer', 'barang_keluars.alamat', 'barangs.name as barang_name', 'barang_keluars.qty', 'barang_keluars.harga');
        if (!empty($request->tgl_awal) && !empty($request->tgl_akhir)) {
            $laporan = $laporan->where('tanggal_keluar', '>=', $request->tgl_awal)->where('tanggal_keluar', '<=', $request->tgl_akhir);
        }
        return $laporan->get();
    }
}
