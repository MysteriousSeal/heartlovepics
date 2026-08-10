<?php

namespace App\Http\Requests;

use App\Support\UploadCountGuard;
use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:125'],
            'is_published' => ['sometimes', 'boolean'],
            'is_private' => ['sometimes', 'boolean'],
            'publish_action' => ['sometimes', 'in:draft,publish'],
            'is_nsfw' => ['sometimes', 'boolean'],
            'content_warning' => ['nullable', 'string', 'max:100'],
            'artist_name' => ['nullable', 'string', 'max:100'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
            'additional_images' => ['nullable', 'array', 'max:99'],
            'additional_images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s\-]+$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Please select an image to upload.',
            'image.image' => 'The file must be a valid image.',
            'image.mimes' => 'Allowed formats: JPEG, PNG, GIF, WebP.',
            'image.max' => 'Images may not be larger than 10 MB.',
            'additional_images.max' => 'You may upload up to 100 images total (1 cover + 99 more).',
            'additional_images.*.image' => 'Each file must be a valid image.',
            'additional_images.*.mimes' => 'Allowed formats: JPEG, PNG, GIF, WebP.',
            'additional_images.*.max' => 'Images may not be larger than 10 MB.',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                UploadCountGuard::check($this, $validator);
            },
        ];
    }
}