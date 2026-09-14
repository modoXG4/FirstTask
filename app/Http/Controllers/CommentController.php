<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ApiResponseTrait;

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
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        if ($request->expectsJson()) {
            return $this->success(new CommentResource($comment->load('user')), 'Comment created successfully', 201);
        }

        return back()->with('success', 'تم إضافة التعليق بنجاح.');
    }
}
