<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Rules\FileTypeValidate;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle', 'user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname'  => 'required|string',
            'lastname'   => 'required|string',
            'image'      => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'coverImage' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required'
        ]);

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        $user->description = $request->description;

        if ($request->hasFile('image')) {
            try {
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $user->image);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('coverImage')) {

            try {
                $user->coverImage = fileUploader($request->coverImage, getFilePath('userCover'), getFileSize('userCover'), $user->coverImage);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function removefavorite($id)
    {
        $favorite = Favorite::where('prompt_id', $id)->where('user_id', auth()->id())->firstOrFail();
        $favorite->delete();
        $notify[] = ['success', 'Removed from favorite successfully'];
        return back()->withNotify($notify);
    }
    public function follow(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'You must be logged in to follow'], 401);
        }

        $followedUserId = $request->input('followed_user_id');
        $userId = auth()->id();

        if ($userId == $followedUserId) {
            return response()->json(['success' => false, 'message' => 'You cannot follow yourself'], 400);
        }


        $existingFollow = Follow::where('user_id', $userId)
            ->where('followed_user_id', $followedUserId)
            ->first();

        if ($existingFollow) {
            $existingFollow->delete();
            $message = 'Unfollowed successfully';
        } else {
            $follow = new Follow();
            $follow->user_id = $userId;
            $follow->followed_user_id = $followedUserId;
            $follow->save();
            $message = 'Followed successfully';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
