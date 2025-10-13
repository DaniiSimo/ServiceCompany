<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth, Hash, RateLimiter, Validator as ValidatorFacade};

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->wantsJson() || $this->ajax();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }


    public function authenticate():void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->input('email'))->first();
        if (is_null($user) || ! Hash::check($this->input('password'), $user?->password)) {
            RateLimiter::hit($this->throttleKey(), 240);

            $v = ValidatorFacade::make([], []); // пустой валидатор
            $v->errors()->add('email', trans('auth.failed'));
            $this->failedValidation($v, $this->rules());
        }

        Auth::login($user);
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited():void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw new HttpResponseException(
            response()->json([
                'status'      => 429,
                'description' => 'Превышен лимит попыток',
                'errors'      => [__('auth.throttle', ['seconds'=>$seconds, 'minutes'=>ceil($seconds/60)])],
            ], 429)->withHeaders(['Retry-After' => $seconds])
        );
    }

    public function throttleKey():string
    {
        return Str::lower(value: $this->input(key:'email')).'|'.$this->ip();
    }

    public function failedValidation(Validator $validator):void
    {
        throw new HttpResponseException(response: response()->json(data:[
            'status' => 422,
            'description' => 'Ошибка валидации',
            'data' => $validator->errors()
        ], status: 422));
    }
}
