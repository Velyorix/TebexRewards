<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Tebexrewards\Models\RankTier;
use Azuriom\Plugin\Tebexrewards\Requests\AdminRankProfileSettingRequest;
use Azuriom\Plugin\Tebexrewards\Requests\AdminRankTierRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RankTierController extends Controller
{
    public function index(): View {

        $tiers = RankTier::query()->orderBy('min_amount')->orderBy('id')->get();

        return view('tebexrewards::admin.ranks', [
            'tiers' => $tiers,
            'editing' => null,
            'profile_card_enabled' => (bool) setting('tebexrewards.ranks.profile_card_enabled', true),
        ]);
    }

    public function edit(RankTier $rankTier): View {

        $tiers = RankTier::query()->orderBy('min_amount')->orderBy('id')->get();

        return view('tebexrewards::admin.ranks', [
            'tiers' => $tiers,
            'editing' => $rankTier,
            'profile_card_enabled' => (bool) setting('tebexrewards.ranks.profile_card_enabled', true),
        ]);
    }

    public function store(AdminRankTierRequest $request): RedirectResponse {

        RankTier::query()->create([
            'rank_name' => (string) $request->input('rank_name'),
            'min_amount' => (float) $request->input('min_amount'),
            'icon' => $this->nullableString($request->input('icon')),
            'color_hex' => $this->nullableString($request->input('color_hex')),
            'enabled' => (bool) $request->boolean('enabled'),
        ]);

        return redirect()
            ->route('tebexrewards.admin.ranks')
            ->with('success', trans('tebexrewards::messages.admin.ranks.created'));
    }

    public function update(AdminRankTierRequest $request, RankTier $rankTier): RedirectResponse {

        $rankTier->update([
            'rank_name' => (string) $request->input('rank_name'),
            'min_amount' => (float) $request->input('min_amount'),
            'icon' => $this->nullableString($request->input('icon')),
            'color_hex' => $this->nullableString($request->input('color_hex')),
            'enabled' => (bool) $request->boolean('enabled'),
        ]);

        return redirect()
            ->route('tebexrewards.admin.ranks')
            ->with('success', trans('tebexrewards::messages.admin.ranks.updated'));
    }

    public function destroy(RankTier $rankTier): RedirectResponse {

        $rankTier->delete();

        return redirect()
            ->route('tebexrewards.admin.ranks')
            ->with('success', trans('tebexrewards::messages.admin.ranks.deleted'));
    }

    public function resetDefaults(): RedirectResponse {

        DB::transaction(function () {
            RankTier::query()->delete();
            foreach (RankTier::defaultTierRows() as $row) {
                RankTier::query()->create($row);
            }
        });

        return redirect()
            ->route('tebexrewards.admin.ranks')
            ->with('success', trans('tebexrewards::messages.admin.ranks.reset_done'));
    }

    public function saveProfileSetting(AdminRankProfileSettingRequest $request): RedirectResponse {

        Setting::updateSettings([
            'tebexrewards.ranks.profile_card_enabled' => (bool) $request->boolean('ranks_profile_card_enabled'),
        ]);

        return redirect()
            ->route('tebexrewards.admin.ranks')
            ->with('success', trans('admin.settings.updated'));
    }

    private function nullableString(mixed $value): ?string {

        if (! is_string($value)) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : $t;
    }
}
