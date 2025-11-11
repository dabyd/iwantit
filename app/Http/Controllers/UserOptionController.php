<?php

namespace App\Http\Controllers;

use App\Models\UserOption;
use Illuminate\Http\Request;

class UserOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userOptions = UserOption::all();
        return view('user_options.index', compact('userOptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user_options.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'option_id' => 'required|exists:options,id',
            'active' => 'required|boolean',
        ]);

        UserOption::create($request->all());

        return redirect()->route('user_options.index')->with('success', 'User Option created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserOption  $userOption
     * @return \Illuminate\Http\Response
     */
    public function show(UserOption $userOption)
    {
        return view('user_options.show', compact('userOption'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserOption  $userOption
     * @return \Illuminate\Http\Response
     */
    public function edit(UserOption $userOption)
    {
        return view('user_options.edit', compact('userOption'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserOption  $userOption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserOption $userOption)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'option_id' => 'required|exists:options,id',
            'active' => 'required|boolean',
        ]);

        $userOption->update($request->all());

        return redirect()->route('user_options.index')->with('success', 'User Option updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserOption  $userOption
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserOption $userOption)
    {
        $userOption->delete();

        return redirect()->route('user_options.index')->with('success', 'User Option deleted successfully.');
    }
}
