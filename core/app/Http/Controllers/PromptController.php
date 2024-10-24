<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\OrderDetails;
use App\Models\Prompt;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromptController extends Controller {
    private $pageTitle;
    private $activeFilter = null;

    public function index() {
        $this->pageTitle  = 'Prompts';
        return $this->promptData('approved');
    }

    public function featured() {
        $this->activeFilter = 'featured';
        $this->pageTitle  = 'Featured Prompts';
        return $this->promptData('featured');
    }

    public function trending() {
        $this->activeFilter = 'trending';
        $this->pageTitle  = 'Trending Prompts';
        return $this->promptData('trending');
    }

    public function similar($slug) {
        $this->pageTitle  = 'Similar Prompts';
        $prompt     = Prompt::where('slug', $slug)->firstOrFail();
        return $this->promptData('similarPrompts', $prompt);
    }

    public function categoryWise($slug) {
        $category   = Category::active()->where('slug', $slug)->firstOrFail();
        $this->pageTitle  =  $category->name;
        $this->activeFilter = 'category';
        return $this->promptData(categoryId: $category->id);
    }

    public function toolWise($slug) {
        $tool       = Tool::active()->where('slug', $slug)->firstOrFail();
        $this->pageTitle  = $tool->name . ' Prompts';
        $this->activeFilter = 'tool';
        return $this->promptData(toolId: $tool->id);
    }

    public function details($slug, Request $request) {
        $pageTitle = 'Prompt Details';
        $slug = htmlspecialchars_decode($slug);

        $prompt   = Prompt::approved()
        ->where('slug', $slug)
        ->with('user', 'category', 'promptImages')
        ->withAvg('reviews', 'rating')
        ->withCount('reviews', 'favorites')
        ->firstOrFail();

        $user = $prompt->user;
        $reviews = $prompt->reviews()->with('user')->orderBy('id', 'DESC')->paginate(5);

        if ($request->ajax()) {
            return response()->json([
                'view' => view('Template::prompt.review', compact('pageTitle', 'reviews'))->render(),
                'nextPageUrl' => $reviews->nextPageUrl()
            ]);
        }

        $hasPurchased = false;
        $userReview = null;
        $isFavorite = false;

        if (auth()->check()) {
            $hasPurchased = OrderDetails::where('prompt_id', $prompt->id)
            ->whereHas('order', function($order){
                $order->where('status', Status::ORDER_COMPLETED)
                ->where('user_id', auth()->id());
            })->exists();

            $userReview = $prompt->reviews()->where('user_id', auth()->id())->first();
            $isFavorite = $prompt->favorites()->where('user_id', auth()->id())->exists();

        }

        if ($prompt->user_id != auth()->id()) {
            $prompt->views += 1;
            $prompt->save();
        }

        $relatedPrompts = Prompt::approved()
            ->where(function ($query) use ($prompt) {
                $query->where('category_id', $prompt->category_id)
                    ->orWhere('tool_id', $prompt->tool_id);
            })
            ->where('id', '!=', $prompt->id)
            ->inRandomOrder()
            ->with('user')
            ->orderBy('id', 'DESC');

        $relatedPromptsCount = (clone $relatedPrompts)->count();
        $relatedPrompts = $relatedPrompts->take(6)->get();

        $allImages = array_merge([$prompt->image], $prompt->promptImages->pluck('image')->toArray());

        $description = $prompt->description;
        $limit = 60;

        if (strlen($description) > $limit) {
            $shortDescription = substr($description, 0, strrpos(substr($description, 0, $limit), ' '));
        } else {
            $shortDescription = $description;
        }

        return view('Template::prompt.details', compact('pageTitle', 'limit', 'isFavorite', 'description', 'shortDescription', 'allImages', 'relatedPromptsCount', 'prompt', 'relatedPrompts', 'reviews', 'hasPurchased', 'userReview'));
    }

    public function promptFilter(Request $request) {

        $validation = Validator::make($request->all(), [
            'sort_by'        => 'nullable|in:trending,featured,latest',
            'tool'           => 'nullable|array',
            'tool.*'         => 'required|exists:tools,id',
            'category'       => 'nullable|array',
            'category.*'     => 'required|exists:categories,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->all()
            ]);
        }

        $prompts = Prompt::approved()->with('category', 'user');

        if ($request->has('category') && $request->category != 0) {
            $prompts->whereIn('category_id', $request->category);
        }

        if ($request->has('tool') && $request->tool != 0) {
            $prompts->whereIn('tool_id', $request->tool);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by;
            $prompts->$sortBy();
        }

        $prompts = $prompts->paginate(getPaginate(24));
        $totalPrompt = $prompts->total();

        $data = [
            'view' => view('Template::partials.prompt', compact('prompts'))->render(),
            'totalPrompt' => $totalPrompt,
        ];

        return response()->json($data);
    }

    public function addFavorite(Request $request) {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'You must be logged in to add favorites'], 401);
        }

        $promptId = $request->input('prompt_id');
        $userId = auth()->id();

        $prompt = Prompt::find($promptId);

        if ($userId == $prompt->user_id) {
            return response()->json(['success' => false, 'message' => 'You cannot add to favorite own prompt'], 400);
        }

        $favorite = Favorite::where('user_id', $userId)
            ->where('prompt_id', $promptId)
            ->first();
        if ($favorite) {
            $favorite->delete();
            return response()->json(['success' => true, 'message' => 'Removed from favorites']);
        } else {
            $favorite = new Favorite;
            $favorite->user_id = $userId;
            $favorite->prompt_id = $promptId;
            $favorite->save();
            return response()->json(['success' => true, 'message' => 'Added to favorites']);
        }
    }

    protected  function promptData($scope = null, $prompt = null, $categoryId = null, $toolId = null) {

        $categories = Category::active()->get();
        $tools      = Tool::active()->get();

        if ($scope) {
            $prompts = $prompt ? Prompt::$scope($prompt) : Prompt::$scope();
        } else {
            $prompts = Prompt::query();
        }

        if ($categoryId) {
            $prompts->where('category_id', $categoryId);
        }

        if ($toolId) {
            $prompts->where('tool_id', $toolId);
        }

        $prompts = $prompts
            ->searchable(['title', 'user:username', 'category:name', 'tool:name'])
            ->filter(['status'])
            ->with('user:.id,username', 'tool:id,name')
            ->approved()
            ->latest()
            ->paginate(getPaginate(24));

        $pageTitle = $this->pageTitle;
        $activeFilter = $this->activeFilter;
        return view('Template::prompt.prompts', compact('pageTitle', 'prompts', 'categories', 'tools', 'activeFilter', 'categoryId', 'toolId'));
    }
}
