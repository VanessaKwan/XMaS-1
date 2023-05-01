<?php

namespace App\Http\Controllers;


use App\Models\userXmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class signUpController extends Controller
{
    public function index()
    {
        return view('Register/signup');
    }

    public function store(Request $request){

        $data = $request -> validate([
            'name' => 'required|min:6|max:25',
            'NIP' => 'required|digits:4|numeric|unique:user_xmas',
            'program' => 'required',
            'phoneNumber' => 'required|min:11|max:13',
            'password' => 'required|min:6',
            'photo' => 'required|image'
        ]);

        $data['photo'] = $request->file('photo')->store('database-assets');
        $data['password'] = Hash::make($data['password']);

        userXmas::create($data);

        return redirect('/login');
    }

    public function home()
    {
        return view('home');

    }
}
