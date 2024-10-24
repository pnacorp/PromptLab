<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'All Review';
        $reviews   = Review::with(['prompt:id,title,slug', 'user'])->searchable(['user:username', 'prompt:title'])->latest()->paginate(getPaginate());
        return view('admin.review.index', compact('pageTitle', 'reviews'));
    }


    public function delete($id)
    {
        Review::where('id', $id)->delete();
        $notify[] = ['success', 'Review removed successfully'];
        return back()->withNotify($notify);
    }
}
