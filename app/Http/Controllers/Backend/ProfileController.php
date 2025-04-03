<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Toastr;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile.index');
    }
    public function updateProfile(Request $request)
    {
       
       $request->validate(
        ['name'=>['required','max:100'],
        'email'=>['required','email', 'unique:users,email,'.Auth::user()->id],
        'image' =>['image','max:2048']
        ] );
        
        $user = User::find(Auth::id());
        
        if($request->hasFile('image')){
            if(File::exists(public_path($user->image))){
                File::delete(public_path($user->image));
            }
            $image = $request->image;
            $imageName = rand().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads'),$imageName);
            
            $path = "/uploads/".$imageName;

            $user->image = $path;
        }
        
        $user->name =$request->name;
        $user->email =$request->email;
        $user->save();
        
        toastr()->success('Profile Updated Successfully');
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
/** Update */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' =>['required','current_password'],
            'password' => ['required','confirmed', 'min:8']
        ]);
        
        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);
        
        toastr()->success('Password Updated Successfully');
        return redirect()->back();
    }
}