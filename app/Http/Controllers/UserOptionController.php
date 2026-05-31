<?php

namespace App\Http\Controllers;

use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $userOptions = UserOption::all();

        return view('user_options.index', compact('userOptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('user_options.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
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
     * @return Response
     */
    public function show(UserOption $userOption)
    {
        return view('user_options.show', compact('userOption'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(UserOption $userOption)
    {
        return view('user_options.edit', compact('userOption'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
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
     * @return Response
     */
    public function destroy(UserOption $userOption)
    {
        $userOption->delete();

        return redirect()->route('user_options.index')->with('success', 'User Option deleted successfully.');
    }
}
