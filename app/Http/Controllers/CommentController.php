<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\DeleteCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store($postId, CommentRequest $request)
    {
        $comment = $this->commentService->createComment($postId, $request->validated());

        return response()->json([
            'message' => 'Successfully added new comment',
            'data' => $comment
        ], 201);
    }

    public function showAll($postId)
    {
        $comments = $this->commentService->getAllComments($postId);
        
        return response()->json([
            'status' => 'Success',
            'data' => $comments
        ], 200);
    }

    public function show($postId, $commentId)
    {
        $comment = $this->commentService->getCommentById($postId, $commentId);

        return response()->json([
            'status' => 'Success',
            'data' => $comment
        ], 200);
    }

    public function update($commentId, UpdateCommentRequest $request)
    {
        $comment = $this->commentService->editCommentById($commentId, $request->validated());

       return response()->json([
            'message' => 'Successfully updated comment',
            'data' => $comment
        ], 200);
    }

    public function destroy($commentId, DeleteCommentRequest $request)
    {
        $comment = $this->commentService->deleteById($commentId);

        return response()->json([
            'message' => 'Successfully deleted comment',
            'data' => $comment
        ], 200);
    }
}
