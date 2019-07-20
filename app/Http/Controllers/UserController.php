<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends Controller
{
    public function index()
    {
    	
    	$users = User::latest()->get();

    	return $users;
    	//return response()->json($users);
    }

    public function store(Request $request)
    {
    	return User::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make('123456'),          
        ]);
    }

    public function update(Request $request, $id)
    {
    	$user = User::findOrFail($id);        

        $user->update($request->all());

        return ['message' => 'Updated the user info'];
    }

    public function destroy($id)
    {
    	$user = User::findOrFail($id);

    	$user->delete();

    	return ['message' => 'Deleted the user'];
    }

    //Muestra los usuarios eliminados por sistema
    public function trash()
    {    	
    	$trash = User::onlyTrashed()->get();

    	return $trash;
    }
}
