<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsMenu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        $menu = $this->route('menu');

        return $menu instanceof CmsMenu && ($this->user()?->can('update', $menu) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.label' => ['required', 'string', 'max:120'],
            'items.*.url' => ['nullable', 'string', 'max:500'],
            'items.*.route_name' => ['nullable', 'string', 'max:120'],
            'items.*.route_params' => ['nullable', 'array'],
            'items.*.target' => ['nullable', Rule::in(['_self', '_blank'])],
            'items.*.sort_order' => ['integer', 'min:0'],
            'items.*.is_active' => ['boolean'],
            'items.*.children' => ['nullable', 'array'],
        ];
    }
}
