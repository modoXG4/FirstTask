<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Comment;
use App\Traits\ExpectsJsonTrait;

class CommentController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests, ExpectsJsonTrait;

    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    public function index(Request $request, Task $task)
    {
        $comments = $task->comments()
            ->with('user')
            ->latest()
            ->get();

        return $this->success(CommentResource::collection($comments));
    }

    public function store(StoreCommentRequest $request, Task $task)
    {
        $comment = $task->comments()->create([
            'content' => $request->validated()['content'],
            'owner_id' => $request->user()->id,
        ]);
        
        return $this->respondBasedOnRequest($request, new CommentResource($comment), 'tasks.show', __('comments.created'), 200, [$task]);
    }
}
