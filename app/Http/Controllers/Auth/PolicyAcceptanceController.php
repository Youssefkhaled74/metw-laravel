<?php

namespace App\Http\Controllers\Auth;

use App\Enum\PageType;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PolicyAcceptance;
use Illuminate\Http\Request;

class PolicyAcceptanceController extends Controller
{
    public function showVendor(Request $request)
    {
        return $this->renderAcceptancePage($request, 'vendor', 'التاجر');
    }

    public function acceptVendor(Request $request)
    {
        return $this->storeAcceptance($request, 'vendor', 'vendor.dashboard');
    }

    public function showShipment(Request $request)
    {
        return $this->renderAcceptancePage($request, 'shipment', 'مستودع الشحن');
    }

    public function acceptShipment(Request $request)
    {
        return $this->storeAcceptance($request, 'warehouse', 'shipment.dashboard');
    }

    private function renderAcceptancePage(Request $request, string $guard, string $accountLabel)
    {
        $account = auth($guard)->user();
        $acceptance = $account?->policyAcceptance;

        if ($account && $acceptance && $acceptance->isCurrent()) {
            return redirect()->intended(route($guard . '.dashboard'));
        }

        $terms = Page::query()->where('type', PageType::TERMS->value)->valid()->latest('updated_at')->first();
        $policy = Page::query()->where('type', PageType::POLICY->value)->valid()->latest('updated_at')->first();
        $version = PolicyAcceptance::currentVersion();

        return view('auth.policy-acceptance', [
            'accountLabel' => $accountLabel,
            'guard' => $guard,
            'terms' => $terms,
            'policy' => $policy,
            'policyVersion' => $version,
        ]);
    }

    private function storeAcceptance(Request $request, string $accountType, string $dashboardRoute)
    {
        $request->validate([
            'acceptance' => ['required', 'accepted'],
        ], [
            'acceptance.accepted' => 'يجب الموافقة على الشروط والسياسات للمتابعة.',
        ]);

        $guard = $accountType === 'vendor' ? 'vendor' : 'shipment';
        $account = auth($guard)->user();

        abort_unless($account, 403);

        $version = PolicyAcceptance::currentVersion();

        $record = $account->policyAcceptance()->firstOrNew([
            'account_type' => $accountType,
        ]);

        $record->policy_version = $version;
        $record->accepted_at = now();
        $record->save();

        return redirect()
            ->intended(route($dashboardRoute))
            ->with('success', 'تم حفظ موافقتك على الشروط والسياسات بنجاح.');
    }
}
