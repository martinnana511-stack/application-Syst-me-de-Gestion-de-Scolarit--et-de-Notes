<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return[
            'email' => ['required','string','email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensurelsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))){
            RateLimiter::hit($this->throttlekey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttlekey());
    }

    public function ensurelsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttlekey(), 5)){
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($thih->throttlekey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle',[
                'seconds' => $seconds,
                'minutes' => ceil($seconds/60),
            ]),
        ]);
    }

    public function throttlekey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}