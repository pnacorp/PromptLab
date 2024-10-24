<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\OrderDetails;
use App\Models\Prompt;
use App\Models\PromptImage;
use App\Models\Review;
use App\Models\Tool;
use App\Models\ToolVersion;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PromptController extends Controller {

    public function create() {
        $pageTitle = 'Create Prompt';
        return view('Template::user.prompt.create', compact('pageTitle'));
    }

    public function step2($slug = null) {
        $pageTitle = 'Prompt Details';
        $categories = Category::active()->orderBy('name', 'ASC')->get();
        $tools = Tool::active()->orderBy('name', 'ASC')->get();
        $prompt = Prompt::where('slug', $slug)->where('user_id', auth()->id())->first();
        return view('Template::user.prompt.step2', compact('pageTitle', 'categories', 'prompt', 'tools'));
    }

    public function step2Edit($slug) {
        session()->put('EDIT_PROMPT', TRUE);
        return $this->step2($slug);
    }

    public function step2Store(Request $request, $slug = null) {
        $validation = Validator::make($request->all(), [
            'title'          => 'required|string|max:255',
            'price'          => 'required|numeric|gt:0',
            'description'    => 'required|string',
            'category_id'    => 'required|exists:categories,id',
            'tool_id'        => 'required|exists:tools,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->all()
            ]);
        }

        $user = auth()->user();

        if ($slug) {
            $prompt = Prompt::where('user_id', $user->id)->where('slug', $slug)->first();
            $prompt->status = Status::PROMPT_PENDING;
        } else {
            $prompt = new Prompt();
            $prompt->user_id = $user->id;
            $prompt->step = 2;
            session()->forget('EDIT_PROMPT');
        }

        $prompt->title = $request->title;
        $prompt->slug = $this->makeSlug($request->title, $prompt?->id);
        $prompt->description = $request->description;
        $prompt->category_id = $request->category_id;
        $prompt->tool_id = $request->tool_id;
        $prompt->price = $request->price;
        $prompt->save();

        return response()->json([
            'success' => true,
            'redirect_url' => route('user.prompt.step3', $prompt->slug)
        ]);
    }

    private function makeSlug($title, $promptId) {
        $baseSlug = slug($title);
        $makeSlug = $baseSlug;
        $counter = 1;

        while (Prompt::where('slug', $makeSlug)
            ->when($promptId, function ($query) use ($promptId) {
                $query->where('id', '!=', $promptId);
            })->exists()
        ) {
            $makeSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $makeSlug;
    }

    public function step3($slug = null) {
        $pageTitle = 'Upload Files';
        $prompt = Prompt::where('slug', $slug)->where('user_id', auth()->id())->first();

        if (!$prompt || $prompt->step < 2) {
            return to_route('user.prompt.step2', $prompt?->slug);
        }

        $versions = ToolVersion::where('tool_id', $prompt->tool_id)->orderBy('name', 'ASC')->get();

        $images = [];

        foreach ($prompt->promptImages as $key => $image) {
            $img['id']  = $image->id;
            $img['src'] = getImage(getFilePath('prompt') . '/' . $image->image, getFileSize('prompt'));
            $images[]   = $img;
        }

        return view('Template::user.prompt.step3', compact('pageTitle', 'versions', 'prompt', 'images'));
    }

    public function step3Store(Request $request, $slug) {
        $user = auth()->user();
        $prompt = Prompt::where('user_id', $user->id)->where('slug', $slug)->first();

        if (!$prompt || $prompt->step < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ]);
        }

        $imageValidation = $prompt->image ? 'nullable' : 'required';

        $validation = Validator::make($request->all(), [
            'tool_version_id'  => 'nullable|exists:tool_versions,id',
            'prompt'           => 'required|string',
            'testing_details'  => 'required|string',
            'instruction'      => 'required|string',
            'image'            => [$imageValidation, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'photos'           => "nullable|array",
            'photos.*'         => ['required', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->all()
            ]);
        }

        if ($slug) {
            $prompt->status = gs('prompt_approval') ? Status::PROMPT_APPROVED : Status::PROMPT_PENDING;
            $prompt->prompt = $request->prompt;
            $prompt->testing_details = $request->testing_details;
            $prompt->instruction = $request->instruction;
            $prompt->tool_version_id = $request->tool_version_id;
            $prompt->step = 3;

            if ($request->hasFile('image')) {
                try {
                    $old           = $prompt->image;
                    $prompt->image = fileUploader($request->image, getFilePath('prompt'), getFileSize('prompt'), $old, getThumbSize('prompt'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }

            if ($request->old) {
                $deleteImage     = PromptImage::where('prompt_id', $prompt->id)->pluck('id')->toArray();
                $differenceArray = array_diff($deleteImage, $request->old);

                foreach ($differenceArray as $value) {
                    $promptImage = promptImage::where('id', $value)->first();
                    FileManager()->removeFile(getFilePath('prompt') . '/' . $promptImage->image);
                    $promptImage->delete();
                }
            }

            if ($request->photos) {
                $this->addExtraImage($request->photos, $prompt->id, $prompt);
            }

            $prompt->save();

            return response()->json([
                'success' => true,
                'redirect_url' => route('user.prompt.step4', $prompt->slug)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    public function step4($slug = null) {
        $pageTitle = 'Complete';
        $prompt = Prompt::where('slug', $slug)->where('user_id', auth()->id())->first();

        if (!$prompt || $prompt->step < 3) {
            return to_route('user.prompt.step3', $prompt?->slug);
        }

        $prompt->step = 4;
        $prompt->save();

        return view('Template::user.prompt.step4', compact('pageTitle', 'prompt'));
    }


    protected function addExtraImage($photos, $promptId) {

        foreach ($photos as $image) {
            $promptImage = new PromptImage();
            try {
                $promptImage->image = fileUploader($image, getFilePath('prompt'), getFileSize('prompt'), null, getThumbSize('prompt'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }

            $promptImage->prompt_id = $promptId;
            $promptImage->save();
        }
    }

    public function myPrompt() {
        $pageTitle = 'My Prompts';
        $prompts = Prompt::where('user_id', auth()->id())
            ->searchable(['title', 'category:name', 'tool:name'])
            ->with('user', 'category', 'tool')
            ->latest()
            ->paginate(getPaginate());
        return view('Template::user.prompt.list', compact('pageTitle', 'prompts'));
    }

    public function favoritePrompt() {
        $pageTitle = 'My Favorite Prompts';

        $prompts = Prompt::whereHas('favorites', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with('user', 'category', 'tool')
            ->paginate(getPaginate());

        return view('Template::user.prompt.favorite', compact('pageTitle', 'prompts'));
    }

    public function downloadPromptFile($slug) {
        $prompt = Prompt::where('slug', $slug)->firstOrFail();

        $hasOrder = OrderDetails::where('prompt_id', $prompt->id)->whereHas('order', function ($order) {
            $order->where('status', Status::ORDER_COMPLETED)->where('user_id', auth()->id());
        })->exists();

        if (!$hasOrder) {
            $notify[] = ['error', 'You are not authorized to download this prompt'];
            return to_route('user.purchase.history')->withNotify($notify);
        }

        $separator = str_repeat("=", 50) . "\n";

        $fileContent = $separator;
        $fileContent .= "Prompt:\n";
        $fileContent .= $separator;
        $fileContent .= wordwrap($prompt->prompt, 80) . "\n\n";

        $fileContent .= $separator;
        $fileContent .= "Testing Details:\n";
        $fileContent .= $separator;
        $fileContent .= wordwrap($prompt->testing_details, 80) . "\n\n";

        $fileContent .= $separator;
        $fileContent .= "Instruction:\n";
        $fileContent .= $separator;
        $fileContent .= wordwrap($prompt->instruction, 80) . "\n\n";

        $fileName = $prompt->title . '.txt';

        return Response::make($fileContent, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function review(Request $request, $slug) {
        $request->validate([
            'rating' => 'required|integer|gt:0|max:5',
            'review' => 'required|string',
        ]);

        $user = auth()->user();

        $prompt = Prompt::where('slug', $slug)->firstOrFail();

        $order = OrderDetails::where('prompt_id', $prompt->id)->whereHas('order', function ($order) {
            $order->where('status', Status::ORDER_COMPLETED)->where('user_id', auth()->id());
        })->first();

        if (!$order) {
            $notify[] = ['error', 'You are not authorized to download this prompt'];
            return to_route('user.purchase.history')->withNotify($notify);
        }

        $review = Review::where('prompt_id', $prompt->id)->where('user_id', $user->id)->first();

        if (!$review) {
            $review = new Review();
        }

        $review->prompt_id = $prompt->id;
        $review->user_id   = $user->id;
        $review->rating    = $request->rating;
        $review->review    = $request->review;
        $review->save();


        $notify[] = ['success', 'Thanks for your review'];
        return back()->withNotify($notify);
    }
}
