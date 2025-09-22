<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string'
        ];
    }

    public function prepareForValidation()
    {
        if ($this->route('postId')) {
            $this->merge([
                'likeable_id' => $this->route('postId'),
                'likeable_type' => Post::class,
            ]);
        }

        if ($this->route('commentId')) {
            $this->merge([
                'likeable_id' => $this->route('commentId'),
                'likeable_type' => Comment::class,
            ]);
        }
    }
}
