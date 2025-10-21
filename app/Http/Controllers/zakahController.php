<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class zakahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function handle(Request $request)
    {

        $currency = $request->query('currency');
        $zakat = $request->query('zakat');

        return view('zakah.form', [
            'currency' => $currency,
            'zakat' => $zakat,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
