<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;


class AdminProfileController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $user = User::find($user->id);

       // Retrieve all notifications for the authenticated user
       $notifications = $user->notifications; 

        return view('admin.profile', compact('user', 'notifications'));
    }

    public function update(Request $request)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
    
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    
        $user->first_name_en = $request->input('first_name');
        $user->last_name_en = $request->input('last_name');
        $user->email = $request->input('email');
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        $user->save();
    
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
    
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $user = Auth::user();
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete('profile_pictures/' . $user->profile_picture);
        }
    
        $profilePicture = $request->file('profile_picture');
        $imageName = 'user_' . $user->id . '.' . $profilePicture->getClientOriginalExtension();
        $contents = file_get_contents($request->file('profile_picture'));
        Storage::disk('public')->put('profile_pictures/' . $imageName, $contents);
        
    
        $user->profile_picture = $imageName;
        $user->save();
    
        return back()->with('success', 'Profile picture updated successfully!');
    }
    
    public function deleteProfilePicture()
    {
        $user = Auth::user();
    
        if ($user->profile_picture) {
            Storage::disk('public')->delete('profile_pictures/' . $user->profile_picture);
            $user->profile_picture = null;
            $user->save();
    
            return back()->with('success', 'Profile picture deleted successfully!');
        }
    
        return back()->with('error', 'No profile picture to delete.');
    }
    
}
