<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RoutingController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return view('index');
        } else {
            return view('donation.index');
        }
    }

    /**
     * Display a view based on first route param
     */
    public function root(Request $request, $first)
    {
        if (View::exists($first)) {
            return view($first);
        }

        return view('pages.404'); // fallback view
    }

    /**
     * Second level route
     */
    public function secondLevel(Request $request, $first, $second)
    {
        $viewPath = $first . '.' . $second;

        if (View::exists($viewPath)) {
            return view($viewPath);
        }

        return view('pages.404'); // fallback view
    }

    /**
     * Livewire route
     */
    public function livewireView($first)
    {
        if (View::exists($first)) {
            return view($first);
        }

        return view('pages.404'); // fallback view
    }

    /**
     * Third level route
     */
    public function thirdLevel(Request $request, $first, $second, $third)
    {
        $viewPath = $first . '.' . $second . '.' . $third;

        if (View::exists($viewPath)) {
            return view($viewPath);
        }

        return view('pages.404'); // fallback view
    }
}
