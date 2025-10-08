<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        DB::table('notifications')->where('id', $id)->update(['read_at' => now()]);
        return back();
    }

    /**
     * Clear all notifications (admin clears global, donor clears own)
     */
    public function clearAll()
    {
        if (auth()->user()->role === 'admin') {
            DB::table('notifications')->delete(); // clear global for admin
        } else {
            auth()->user()->notifications()->delete();
        }

        return back();
    }
}
