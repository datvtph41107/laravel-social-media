<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

class UpdatePostRequest extends StorePostRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Todo change later
        // Route::put('/post/{post}', [PostController::class, 'update'])
        $post = $this->route('post');
        return $post->user_id === Auth::id();
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'deleted_file_id' => 'array',
            'deleted_file_id.*' => 'numeric',
        ]);
    }
}
