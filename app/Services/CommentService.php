<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function createComment($postId, array $data)
    {
        $data['user_id'] = Auth::id();

        return $this->commentRepository->store($postId, $data);
    }

    public function getAllComments($postId)
    {
        return $this->commentRepository->getAll($postId);
    }

    public function getCommentById($postId, $commentId)
    {
        return $this->commentRepository->getId($postId, $commentId);
    }

    public function editCommentById($commentId, array $data)
    {
        return $this->commentRepository->update($commentId, $data);
    }

    public function deleteById($commentId)
    {
        return $this->commentRepository->delete($commentId);
    }
}
