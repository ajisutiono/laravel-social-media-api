<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function store(PostRequest $request)
    {
        $post = $this->postService->createPost($request->validated());

        return response()->json([
            'message' => 'Successfully added new post',
            'data' => $post
        ], 201);
    }
}
