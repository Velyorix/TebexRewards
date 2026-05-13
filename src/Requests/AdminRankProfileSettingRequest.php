<?php

namespace Azuriom\Plugin\Tebexrewards\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRankProfileSettingRequest extends FormRequest {

    public function authorize(): bool {
        return $this->user()?->can('admin.access') ?? false;
    }

    protected function prepareForValidation(): void {

        $this->merge([
            'ranks_profile_card_enabled' => filter_var(
                $this->input('ranks_profile_card_enabled'),
                FILTER_VALIDATE_BOOLEAN
            ),
        ]);
    }

    public function rules(): array {

        return [
            'ranks_profile_card_enabled' => ['required', 'boolean'],
        ];
    }
}
