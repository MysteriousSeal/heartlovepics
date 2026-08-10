<?php

namespace App\Http\Requests;

use App\Support\HtmlSanitizer;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public const BIO_MAX_LENGTH = 500;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($this->user()->id),
                Rule::notIn(['admin']),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:8192'],
            'bio' => [
                'nullable',
                'string',
                // Raw ceiling so deeply nested markup can't be used to bloat the
                // column even when it collapses to very little visible text.
                'max:20000',
                $this->bioLengthRule(),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * The bio is rich text, so the limit applies to what the user actually
     * typed rather than to the surrounding markup.
     */
    private function bioLengthRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_string($value)) {
                return;
            }

            $length = mb_strlen(HtmlSanitizer::toPlainText($value));

            if ($length > self::BIO_MAX_LENGTH) {
                $fail('The bio must not be greater than '.self::BIO_MAX_LENGTH.' characters.');
            }
        };
    }
}