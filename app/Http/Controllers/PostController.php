<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $param = request()->input('param');

        if ($param) {
            $employees = Employees::whereIn('roles', [1, 2])->where('name', 'like', '%' . $param . '%')->get();
        } else {
            $employees = Employees::whereIn('roles', [1, 2])->get();
        }

        if ($employees->isEmpty()) {
            return response()->json(['message' => 'Employees not found'], 404);
        } else {
            return $employees;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Employees::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employees = Employees::findOrFail($id);
        $employees->update($request->all());
        return $employees;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
