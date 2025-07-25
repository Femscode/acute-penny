<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\BankDetailsUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

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
        $user = $request->user();
        $validated = $request->validated();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::delete($user->profile_image);
            }
            
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'profile-updated');
    }

    /**
     * Update user's bank details
     */
    public function updateBankDetails(BankDetailsUpdateRequest $request): RedirectResponse
    {
        try {
            $request->user()->update($request->validated());
            return Redirect::route('profile.edit')->with('success', 'bank-details-updated');
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['bank_details' => 'Failed to update bank details. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Update user's password
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return Redirect::route('profile.edit')->with('success', 'password-updated');
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['password_update' => 'Failed to update password. Please try again.'], 'updatePassword')
                ->withInput();
        }
    }

    /**
     * Remove profile image
     */
    public function removeProfileImage(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->profile_image) {
            Storage::delete($user->profile_image);
            $user->update(['profile_image' => null]);
        }

        return Redirect::route('profile.edit')->with('success', 'profile-image-removed');
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

        // Delete profile image if exists
        if ($user->profile_image) {
            Storage::delete($user->profile_image);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}