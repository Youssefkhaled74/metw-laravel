<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WhatsappTemplateController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.whatsapp-templates.index')) {
            return view('dashboard.admin.no-permission');
        }

        WhatsappTemplate::ensureFixedTemplatesExist();

        $keys = WhatsappTemplate::fixedKeys();
        $savedTemplates = WhatsappTemplate::query()->whereIn('key', $keys)->get()->keyBy('key');

        $templateRows = [];
        foreach ($keys as $key) {
            $templateRows[] = [
                'key' => $key,
                'label' => WhatsappTemplate::label($key),
                'hints' => WhatsappTemplate::hints($key),
                'content' => $savedTemplates[$key]->content ?? WhatsappTemplate::defaultContent($key),
            ];
        }

        return view('dashboard.admin.settings.whatsapp-templates.index', compact('templateRows'));
    }

    public function update(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.whatsapp-templates.update')) {
            return view('dashboard.admin.no-permission');
        }

        $keys = WhatsappTemplate::fixedKeys();
        $incomingTemplates = (array) $request->input('templates', []);
        $allowedTemplates = array_intersect_key($incomingTemplates, array_flip($keys));

        if (empty($allowedTemplates)) {
            return redirect()->route('admin.settings.whatsapp-templates.index')
                ->with('error', app()->getLocale() === 'ar' ? 'لم يتم إرسال قالب صالح للحفظ.' : 'No valid template was submitted.');
        }

        $validationRules = [];
        foreach (array_keys($allowedTemplates) as $key) {
            $validationRules["templates.$key"] = ['required', 'string', 'max:10000'];
        }

        $validated = $request->validate($validationRules);

        $placeholderErrors = [];

        foreach (array_keys($allowedTemplates) as $key) {
            $content = (string) ($validated['templates'][$key] ?? '');
            $allowedPlaceholders = WhatsappTemplate::hints($key);
            preg_match_all('/\{[a-z_]+\}/', $content, $matches);
            $foundPlaceholders = array_values(array_unique($matches[0] ?? []));

            $invalidPlaceholders = array_values(array_diff($foundPlaceholders, $allowedPlaceholders));
            $missingPlaceholders = array_values(array_diff($allowedPlaceholders, $foundPlaceholders));

            if (!empty($invalidPlaceholders)) {
                $placeholderErrors["templates.$key"] = app()->getLocale() === 'ar'
                    ? 'يوجد متغيرات غير مسموح بها: ' . implode(' ', $invalidPlaceholders)
                    : 'Contains unsupported placeholders: ' . implode(' ', $invalidPlaceholders);
                continue;
            }

            if (!empty($missingPlaceholders)) {
                $placeholderErrors["templates.$key"] = app()->getLocale() === 'ar'
                    ? 'لا يمكن حذف المتغيرات الأساسية. المتغيرات الناقصة: ' . implode(' ', $missingPlaceholders)
                    : 'Required placeholders are missing: ' . implode(' ', $missingPlaceholders);
            }
        }

        if (!empty($placeholderErrors)) {
            throw ValidationException::withMessages($placeholderErrors);
        }

        foreach (array_keys($allowedTemplates) as $key) {
            WhatsappTemplate::query()->updateOrCreate(
                ['key' => $key],
                [
                    'content' => $validated['templates'][$key] ?? WhatsappTemplate::defaultContent($key),
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('admin.settings.whatsapp-templates.index')
            ->with('success', __('admin-dashboard.whatsapp_templates_saved'));
    }
}
