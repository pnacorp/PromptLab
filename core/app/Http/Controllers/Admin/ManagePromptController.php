<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Prompt;
use App\Models\User;

class ManagePromptController extends Controller
{
    public function index($userId = null)
    {
        $pageTitle = 'All Prompts';
        $prompts      = $this->promptData(userId:$userId);
        return view('admin.prompt.index', compact('pageTitle', 'prompts'));
    }

    public function pending($userId = null)
    {
        $pageTitle = "Pending Prompts";
        $prompts = $this->promptData('pending',userId:$userId);
        return view('admin.prompt.index', compact('pageTitle', 'prompts'));
    }
    public function approved($userId = null)
    {
        $pageTitle = "Approved Prompts";
        $prompts = $this->promptData('approved',userId:$userId);
        return view('admin.prompt.index', compact('pageTitle', 'prompts'));
    }
    public function rejected($userId = null)
    {
        $pageTitle = "Rejected Prompts";
        $prompts = $this->promptData('rejected');
        return view('admin.prompt.index', compact('pageTitle', 'prompts'));
    }
    public function trending($userId = null)
    {
        $pageTitle = "Trending Prompts";
        $prompts = $this->promptData('trending');
        return view('admin.prompt.index', compact('pageTitle', 'prompts'));
    }
    public function featured($userId = null)
    {
        $pageTitle = "Featured Prompts";
        $prompts = $this->promptData('featured');
        return view('admin.prompt.index', compact('pageTitle', 'prompts'));
    }

    protected function promptData($scope = null,$userId = null)
    {
        if ($scope) {
            $prompts = Prompt::$scope()->where('step', '>=', 3);
        } else {
            $prompts = Prompt::query()->where('step', '>=', 3);
        }

        if ($userId) {
            $prompts = $prompts->where('user_id',$userId);
        }

        return $prompts->searchable(['title', 'user:username', 'category:name', 'tool:name'])->filter(['status'])->with('user', 'category', 'tool')->orderBy('id', 'DESC')->paginate(getPaginate());
    }

    public function details($slug)
    {
        $pageTitle = 'Prompt Details';
        $prompt    = Prompt::where('slug', $slug)->with('user', 'category', 'promptImages')->firstOrFail();
        $allImages = array_merge([$prompt->image], $prompt->promptImages->pluck('image')->toArray());

        return view('admin.prompt.details', compact('pageTitle', 'allImages', 'prompt'));
    }

    public function approve($id)
    {
        $prompt  = Prompt::whereIn("status", [Status::PROMPT_REJECTED, Status::PROMPT_PENDING])->findOrFail($id);
        $user = $prompt->user;
        $user = User::active()->where('id', $prompt->user_id)->first();
        if (!$user) {
            $notify[] = ['error',  "Prompt owner is banned now!"];
            return back()->withNotify($notify);
        }
        $prompt->status = Status::PROMPT_APPROVED;
        $prompt->save();

        notify($user, 'PROMPT_APPROVED', [
            'prompt_title'    => $prompt->title,
            'prompt_price'    => showAmount($prompt->price, currencyFormat:false),
            'prompt_tool'     => $prompt->tool->name,
            'prompt_category' => $prompt->category->name,
        ]);

        $notify[] = ['success',  "Prompt approved successfully"];
        return back()->withNotify($notify);
    }

    public function reject($id)
    {
        $prompt  = Prompt::whereIn("status", [Status::PROMPT_APPROVED, Status::PROMPT_PENDING])->findOrFail($id);
        $prompt->status = Status::PROMPT_REJECTED;
        $prompt->save();

        notify($prompt->user, 'PROMPT_REJECTED', [
            'prompt_title'    => $prompt->title,
            'prompt_price'    => showAmount($prompt->price, currencyFormat:false),
            'prompt_tool'     => $prompt->tool->name,
            'prompt_category' => $prompt->category->name,
        ]);

        $notify[] = ['success',  "Prompt rejected successfully"];
        return back()->withNotify($notify);
    }

    public function feature($id)
    {
        $prompt     = Prompt::findOrFail($id);

        if ($prompt->is_featured == Status::YES) {
            $prompt->is_featured = Status::NO;
            $message       ='Unfeatured successfully';
        } else {
            $prompt->is_featured = Status::YES;
            $message       ='Featured successfully';
        }

        $prompt->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

}
