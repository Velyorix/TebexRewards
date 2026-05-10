<?php

namespace Azuriom\Plugin\Tebexrewards\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;

class TebexRewardsSettingsRequest extends FormRequest {

    use ConvertCheckbox;

    /**
     * @var array<int, string>
     */
    protected array $checkboxes = [
        'maintenance_mode',
        'leaderboard_medals',
        'leaderboard_avatars',
        'goal_enabled',
        'goal_reset_enabled',
        'last_enabled',
        'last_show_package',
        'last_show_amount',
        'last_animation',
    ];

    public function authorize(): bool {
        return $this->user()?->can('admin.access') ?? false;
    }

    protected function prepareForValidation(): void {
        $this->mergeCheckboxes();
    }

    public function rules(): array {
        return [
            'tebex_api_key' => ['nullable', 'string', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'sync_interval' => ['required', 'integer', 'in:5,10,30,60'],
            'maintenance_mode' => ['nullable', 'boolean'],

            'leaderboard_limit' => ['required', 'integer', 'min:5', 'max:100'],
            'leaderboard_period' => ['required', 'string', 'in:all,month,week,day'],
            'leaderboard_columns' => ['nullable', 'array'],
            'leaderboard_columns.*' => ['string', 'in:rank,player,amount,purchases'],
            'leaderboard_medals' => ['nullable', 'boolean'],
            'leaderboard_avatars' => ['nullable', 'boolean'],

            'goal_enabled' => ['nullable', 'boolean'],
            'goal_target' => ['required', 'numeric', 'min:0'],
            'goal_currency' => ['required', 'string', 'max:8'],
            'goal_color_start' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'goal_color_mid' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'goal_color_end' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'goal_message_reached' => ['required', 'string', 'max:255'],
            'goal_reset_enabled' => ['nullable', 'boolean'],
            'goal_reset_increment' => ['required', 'numeric', 'min:0'],

            'last_enabled' => ['nullable', 'boolean'],
            'last_show_package' => ['nullable', 'boolean'],
            'last_show_amount' => ['nullable', 'boolean'],
            'last_timestamp' => ['required', 'string', 'in:relative,absolute'],
            'last_animation' => ['nullable', 'boolean'],
            'last_animation_speed' => ['required', 'integer', 'min:100', 'max:5000'],
        ];
    }

    public function attributes(): array {
        return [
            'tebex_api_key' => trans('tebexrewards::messages.admin.fields.tebex_api_key'),
            'webhook_secret' => trans('tebexrewards::messages.admin.fields.webhook_secret'),
            'sync_interval' => trans('tebexrewards::messages.admin.fields.sync_interval'),
            'maintenance_mode' => trans('tebexrewards::messages.admin.fields.maintenance'),
            'leaderboard_limit' => trans('tebexrewards::messages.admin.fields.leaderboard_limit'),
            'leaderboard_period' => trans('tebexrewards::messages.admin.fields.leaderboard_period'),
            'goal_target' => trans('tebexrewards::messages.admin.fields.goal_target'),
            'goal_currency' => trans('tebexrewards::messages.admin.fields.goal_currency'),
            'goal_message_reached' => trans('tebexrewards::messages.admin.fields.goal_message_reached'),
            'last_timestamp' => trans('tebexrewards::messages.admin.fields.last_timestamp'),
        ];
    }
}

