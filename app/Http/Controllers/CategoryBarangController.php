<?php
namespace App\Http\Controllers;

use App\Models\CategoryBarang;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CategoryBarangController extends Controller
{
    protected $user;
 
    public function __construct() 
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index() 
    {
        return CategoryBarang::get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->only('name');
        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $CategoryBarang = CategoryBarang::create([
            'name' => $request->name
        ]);
        return response()->json([
            'success' => true,
            'message' => 'CategoryBarang created successfully',
            'data' => $CategoryBarang
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $CategoryBarang = CategoryBarang::find($id);
        if (!$CategoryBarang) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, CategoryBarang not found.'
            ], 400);
        }
        return $CategoryBarang;
    }

    public function edit(CategoryBarang $CategoryBarang)
    {
        //
    }

    public function update(Request $request, CategoryBarang $CategoryBarang)
    {
        $data = $request->only('name');
        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $CategoryBarang = $CategoryBarang->update([
            'name' => $request->name
        ]);
        return response()->json([
            'success' => true,
            'message' => 'CategoryBarang updated successfully',
            'data' => $CategoryBarang
        ], Response::HTTP_OK);
    }

    public function destroy(CategoryBarang $CategoryBarang)
    {
        $CategoryBarang->delete();
        return response()->json([
            'success' => true,
            'message' => 'CategoryBarang deleted successfully'
        ], Response::HTTP_OK);
    }
}