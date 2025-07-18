<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function Index()
    {
        return view('frontend.index');
    }

    public function home()
    {
        $categories = Category::latest()->limit(5)->get();
        $courses = Course::where('status', 1)->orderBy('id', 'DESC')->limit(5)->get();
        return view('new_frontend.home', compact('categories', 'courses'));
    }

    public function UserProfile()
    {

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('frontend.dashboard.edit_profile', compact('profileData'));
    } // End Method 

    public function UserProfileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();
        $user = user::findorFail($user->id);

        $data = $request->only(['name', 'username', 'email', 'phone', 'address']);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('upload/user_images', 'public');

            $data['photo'] = 'storage/' . $path;
        }

        $user->update($data);

        return back()->with([
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Logout Successfully',
            'alert-type' => 'info'
        );

        return redirect('/login')->with($notification);
    } // End Method 


    public function UserChangePassword()
    {
        return view('frontend.dashboard.change_password');
    } // End Method 


    public function UserPasswordUpdate(Request $request)
    {

        /// Validation 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {

            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        /// Update The new Password 
        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    } // End Method


    public function LiveChat()
    {
        return view('frontend.dashboard.live_chat');
    } // End Method 
}
