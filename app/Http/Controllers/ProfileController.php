<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\FlashMessage;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function show()
    {
        return view('profile.show', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update profile details
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|in:mr,mme,mlle,dr',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'required|in:male,female,other',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:50',
            'country' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'birth_country' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $user->update($validated);

        return back()->with('success', FlashMessage::translated('messages.profile_updated'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('messages.current_password_invalid')]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return back()->with('success', FlashMessage::translated('messages.password_updated'));
    }
}
