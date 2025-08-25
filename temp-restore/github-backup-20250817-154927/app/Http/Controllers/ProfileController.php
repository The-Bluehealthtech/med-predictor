<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $this->updateProfilePicture($request);
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update profile picture
     */
    private function updateProfilePicture(Request $request): void
    {
        $request->validate([
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = $request->user();
        
        // Delete old profile picture if exists
        if ($user->profile_picture_url && !str_contains($user->profile_picture_url, 'dicebear.com')) {
            Storage::delete($user->profile_picture_url);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        $user->profile_picture_url = $path;
        $user->profile_picture_alt = $user->name . ' Profile Picture';
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's settings form.
     */
    public function settings(Request $request): View
    {
        return view('profile.settings', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'timezone' => 'string|max:50',
            'language' => 'string|max:10',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
        ]);

        $request->user()->update($request->only([
            'timezone', 'language', 'notifications_email', 'notifications_sms'
        ]));

        return Redirect::route('profile.settings')->with('status', 'settings-updated');
    }
}
