<?php

namespace App\Http\Requests\Intranet;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIntranetUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        return $target instanceof User && ($this->user()?->can('update', $target) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(IntranetRole::values())],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var User|null $target */
            $target = $this->route('user');
            $actor = $this->user();
            if ($target === null || $actor === null) {
                return;
            }

            if ($actor->id === $target->id) {
                if ($actor->hasRole(IntranetRole::Administrador->value)
                    && $this->input('role') !== IntranetRole::Administrador->value
                ) {
                    $validator->errors()->add(
                        'role',
                        'No puede cambiar el rol Administrador de su propia cuenta desde este formulario.',
                    );
                }

                if (! $this->boolean('is_active')) {
                    $validator->errors()->add(
                        'is_active',
                        'No puede desactivar su propia cuenta.',
                    );
                }
            }
        });
    }
}
