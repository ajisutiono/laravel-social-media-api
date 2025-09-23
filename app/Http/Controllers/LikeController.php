<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use App\Services\LikeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    public function store(LikeRequest $request)
    {
        $like = $this->likeService->addLike([
            'user_id' => Auth::id(),
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $request->likeable_type,
        ]);

        return response()->json([
            'message' => 'Liked sucessfully',
            'data' => $like,
        ], 201);
    }

    public function showAll(LikeRequest $request)
    {
        $likes = $this->likeService->showLikes([
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $request->likeable_type,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $likes,
        ], 200);
    }

    public function destroy(LikeRequest $request)
    {
        $like = $this->likeService->removeLike([
            'user_id' => Auth::id(),
            'likeable_id' => $request->likeable_id,
            'likeable_type' => $request->likeable_type,
        ]);

        return response()->json([
            'message' => 'Unliked sucessfully',
            'data' => $like
        ], 200);
    }
}
