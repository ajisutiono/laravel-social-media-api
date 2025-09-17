<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
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

    public function showAll()
    {
        $posts = $this->postService->getAllPost();

        return response()->json([
            'status' => 'Success',
            'data' => $posts
        ], 200);
    }

    public function show($postId)
    {
        $post = $this->postService->getByIdPost($postId);

        return response()->json([
            'status' => 'Success',
            'data' => $post
        ], 200);
    }

    public function edit(UpdatePostRequest $request, $postId)
    {
        $post = $this->postService->editByIdPost($postId, $request->validated());

        return response()->json([
            'message' => 'Successfully updated post',
            'data' => $post
        ], 200);
    }
}
