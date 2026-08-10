<?php

namespace App\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadCountGuard
{
    /**
     * PHP silently truncates $_FILES beyond php.ini's max_file_uploads (default 20)
     * without raising a catchable error — a multi-image post can end up missing
     * files with no indication to the user. The client tells us how many files it
     * actually attempted to send via `expected_file_count`; if fewer arrived, fail
     * loudly instead of saving a post with images silently missing.
     */
    public static function check(FormRequest $request, Validator $validator): void
    {
        $expected = (int) $request->input('expected_file_count', 0);

        if ($expected <= 0) {
            return;
        }

        $received = ($request->hasFile('image') ? 1 : 0) + count($request->file('additional_images', []) ?? []);

        if ($received < $expected) {
            $validator->errors()->add(
                'additional_images',
                "Your browser tried to send {$expected} images, but the server only received {$received}. ".
                'This usually means the server\'s upload limit was reached — try uploading fewer images at once, or contact the site administrator.',
            );
        }
    }
}
