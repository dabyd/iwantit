<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $languages = Language::latest()->paginate(5);
        $controller = $this;

        return view('languages.index', compact('languages', 'controller'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $controller = $this;

        return view('languages.create', compact('controller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $prj = $request->all();
        unset($prj['_token']);

        Language::create($prj);

        return redirect()->route('languages.index')->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Language $language)
    {
        $controller = $this;

        return view('languages.show', compact('language', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Language $language)
    {
        $controller = $this;

        return view('languages.edit', compact('language', 'controller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $prj = $request->all();
        unset($prj['_token']);
        $language->update($prj);

        return redirect()->route('languages.index')->with('success', 'Language updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Language $language)
    {
        $language->delete();

        return redirect()->route('languages.index')->with('success', 'Language deleted successfully');
    }

    public function getParams($data = '')
    {
        $params = [];
        $params['view'] = 'languages';
        $params['singular'] = 'language';
        $params['plural'] = 'Languages';
        $params['fields'] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false,
            ],
            [
                'label' => 'Name',
                'name' => 'name',
                'editable' => true,
                'type' => 'text',
            ],
        ];
        $ret = $params;
        if ($data != '' && isset($params[$data])) {
            $ret = $params[$data];
        }

        return $ret;
    }
}
