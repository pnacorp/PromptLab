<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolVersion;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index()
    {
        $pageTitle  = 'All Tools';
        $tools = Tool::searchable(['name'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.tool.index', compact('pageTitle', 'tools'));
    }


    public function store(Request $request, $id = 0)
    {
        $request->validate(
            [
                'name'     => 'required'
            ]
        );


        if ($id) {
            $tool          = Tool::findOrFail($id);
            $notification  = 'Tool updated successfully';
        } else {
            $tool          = new Tool();
            $notification  = 'Tool added successfully';
        }

        $baseSlug = slug($request->name);
        $slug = $baseSlug;
        $counter = 1;

        while (Tool::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $tool->name = $request->name;
        $tool->slug = $slug;
        $tool->save();
        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function version($id)
    {
        $tool = Tool::findOrfail($id);
        $pageTitle  = $tool->name . ' Versions';
        $versions = ToolVersion::searchable(['name'])->where('tool_id', $tool->id)->latest()->paginate(getPaginate());

        return view('admin.tool.version', compact('pageTitle', 'tool', 'versions'));
    }

    public function versionStore(Request $request, $id = 0)
    {
        $request->validate(
            [
                'name'  => 'required',
                'tool_id'  => 'required|exists:tools,id',
            ]
        );


        if ($id) {
            $version     = ToolVersion::findOrFail($id);
            $notification = 'Tool version updated successfully';
        } else {
            $version     = new ToolVersion();
            $version->tool_id = $request->tool_id;
            $notification = 'Tool version added successfully';
        }

        $version->name = $request->name;
        $version->save();
        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Tool::changeStatus($id);
    }

    public function versionStatus($id)
    {
        return ToolVersion::changeStatus($id);
    }

}
