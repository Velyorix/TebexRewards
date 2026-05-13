<?php

namespace Azuriom\Plugin\Tebexrewards\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;

class AdminRankTierRequest extends FormRequest {

    use ConvertCheckbox;

    /**
     * @var array<int, string>
     */
    protected array $checkboxes = [
        'enabled',
    ];

    public function authorize(): bool {

        return $this->user()?->can('admin.access') ?? false;
    }

    protected function prepareForValidation(): void {

        $this->mergeCheckboxes();

        $color = $this->input('color_hex');
        if (is_string($color) && trim($color) === '') {
            $this->merge(['color_hex' => null]);
        }

        $icon = $this->input('icon');
        if (is_string($icon) && trim($icon) === '') {
            $this->merge(['icon' => null]);
        }
    }

    public function rules(): array {

        return [
            'rank_name' => ['required', 'string', 'max:64'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color_hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'enabled' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array {

        return [
            'rank_name' => trans('tebexrewards::messages.admin.ranks.fields.name'),
            'min_amount' => trans('tebexrewards::messages.admin.ranks.fields.min_amount'),
            'icon' => trans('tebexrewards::messages.admin.ranks.fields.icon'),
            'color_hex' => trans('tebexrewards::messages.admin.ranks.fields.color_hex'),
        ];
    }
}
