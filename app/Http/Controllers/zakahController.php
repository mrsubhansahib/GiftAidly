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
}
