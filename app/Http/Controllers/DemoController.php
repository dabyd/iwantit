<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Project;
use Illuminate\Support\Facades\URL;

class DemoController extends Controller
{
    private function getDemoData($code)
    {
        $project = Project::where('demo_code', $code)->first();

        if (! $project) {
            abort(404, 'Project not found.');
        }

        $license = License::where('versions_id', $project->id)
            ->where('disabled', '0')
            ->first();

        return [
            'project' => $project,
            'video_url' => URL::asset('uploads/'.$project->filename),
            'cover_url' => $project->cover ? URL::asset('uploads/'.$project->cover) : null,
            'license_key' => $license ? $license->key : '',
        ];
    }

    public function index($code)
    {
        $data = $this->getDemoData($code);

        return view('demo.index', $data);
    }

    public function fullscreen($code)
    {
        $data = $this->getDemoData($code);

        return view('demo.fullscreen', $data);
    }

    public function grid($code)
    {
        $data = $this->getDemoData($code);

        return view('demo.grid', $data);
    }

    public function grid3x3($code)
    {
        $data = $this->getDemoData($code);

        return view('demo.grid-3x3', $data);
    }
}
