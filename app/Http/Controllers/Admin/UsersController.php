<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Gate;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin/users/index', [
          'users' => $users
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
      if(Gate::denies('edit-users')){
        return redirect(route('admin.users.index'));
      }
      $role = Role::all();
      // dd($user, $role);
        return view('admin.users.edit', [
            'user' => $user,
            'role' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // dd($request);
        $user->roles()->sync($request->roles);

        $user->name = $request->name;
        $user->email = $request->email;
        // $user->save();

        if($user->save())
        {
          $request->session()->flash('success', $user->name . ' has been uploaded');
        }
        else {
          $request->session()->flash('error', 'There was an error updating the user');
        }

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
      if(Gate::denies('delete-users')){
        return redirect(route('admin.users.index'));
      }
        $user->roles()->detach();

        $user->delete();

        return redirect()->route('admin.users.index');
    }

    // Test
    // public function hasRole($role)
    // {
    //   $user = User::find(1);
    //
    //   return $user->roles()->where('name', 'admin')->first();
    // }
}
