<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_users')->only('index');
        $this->middleware('permission:create_users')->only('create');
        $this->middleware('permission:update_users')->only('edit');
        $this->middleware('permission:delete_users')->only('destroy');
    }

    public function index(Request $request)
    {
        $users = User::search($request)->paginate();

        return view('dashboard.users.index', compact('users'));
    }


    public function create()
    {
        return view('dashboard.users.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'permissions' => 'nullable'
        ]);

        if ($request->has('image')) {
            $image = $request->image->hashName();
            $data['password'] = Hash::make($data['password']);
            $data['image'] = $image;
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/user_images/' . $image));
        }

        $user = User::create($data);
        $user->attachRole('admin');
        $user->syncPermissions($data['permissions']);



        session()->flash('success', Lang::get('site.added_successfully'));

        return redirect()->route('dashboard.users.index');
    }


    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required',Rule::unique('users')->ignore($user->id)],
            'permissions' => 'nullable'
        ]);

        if ($request->has('image')) {

            if ($user->image != 'default.jpg') {
                Storage::disk('public_uploads')->delete('user_images/' . $user->image);
            }

            $image = $request->image->hashName();
            $data['image'] = $image;
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/user_images/' . $image));
        }

        $user->fill($data)->save();
        $user->syncPermissions($data['permissions']);

        session()->flash('success', Lang::get('site.updated_successfully'));

        return redirect()->route('dashboard.users.index');
    }

    public function destroy(User $user)
    {
        if ($user->image != 'default.jpg') {
            Storage::disk('public_uploads')->delete('user_images/' . $user->image);
        }
        $user->delete();
        session()->flash('success', Lang::get('site.deleted_successfully'));

        return redirect()->route('dashboard.users.index');
    }
}
