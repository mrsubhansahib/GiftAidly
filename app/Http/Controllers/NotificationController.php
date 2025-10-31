<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    public function clearAll(Request $request)
    {
        // Admin: clear all notifications
        if (auth()->check() && auth()->user()->role === 'admin') {
            DB::table('notifications')->delete();
            return back();
        }
        // Donor: clear notifications by reference_id
        if ($request->filled('reference_id')) {
            User::where('reference_id', $request->reference_id)
                ->first()?->notifications()->delete();
        }
        return back();
    }
}
