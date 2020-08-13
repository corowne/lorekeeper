<?php

namespace App\Http\Controllers;

use Auth;
use File;
use Image;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{

    protected $user;

    public function profile(){
            return view('user.profile', [
                'user' => $this->user,
                'items' => $this->user->items()->orderBy('user_items.updated_at', 'DESC')->take(4)->get()
            ]);
        }

        public function update_avatar(Request $request)
        {
            $user = User::find(Auth::user()->id);
    
            // Handle the user upload of avatar
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = $user->id . '.' . $avatar->getClientOriginalExtension();
                //$storagePath = Storage::disk('public')->put( '/avatars', $avatar);

                if ($user->avatar !== 'default.jpg') {
                    // $file = public_path('uploads/avatars/' . $user->avatar);
                    $file = 'images/avatars/' . $user->avatar;
                    //$destinationPath = 'uploads/' . $id . '/';
    
                    if (File::exists($file)) {
                        unlink($file);
                    }
                }

                // Checks if uploaded file is a GIF
                if ($avatar->getClientOriginalExtension() == 'gif') {
            
                    copy($avatar, $file); 
                    $avatar->move( public_path('images/avatars', $filename));
                    
                }

                // Image::make($avatar)>resize(300, 300)>save(public_path('uploads/avatars/' . $filename));
                else (Image::make($avatar)->resize(150, 150)->save( public_path('images/avatars/' . $filename)));
                
                    $user = Auth::user();
                    $user->avatar = $filename;
                    $user->save();
    
            {
                flash('Avatar updated successfully.')->success();
                return redirect()->back();
            }
        }
    }
}