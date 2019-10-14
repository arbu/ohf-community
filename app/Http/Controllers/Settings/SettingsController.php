<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Setting;

abstract class SettingsController extends Controller
{
    protected abstract function getSections();

    protected abstract function getSettings();

    protected abstract function getRedirectRouteName();

    protected abstract function getUpdateRouteName();

    /**
     * View for configuring settings.
     * 
     * @return \Illuminate\Http\Response
     */
    function edit() {
        return view('settings', [
            'route' => $this->getUpdateRouteName(),
            'sections' => $this->getSections(),
            'fields' => collect($this->getSettings())
                ->filter()
                ->mapWithKeys(function($e, $k){ 
                    $value = Setting::get($k, $e['default']);
                    if ($value != null && isset($e['getter']) && is_callable($e['getter'])) {
                        $value = $e['getter']($value);
                    }
                    return [
                        Str::slug($k) => [
                            'value' => $value,
                            'type' => $e['form_type'],
                            'label' => __($e['label_key']),
                            'section' => $e['section'] ?? null,
                            'args' => $e['form_args'] ?? null,
                            'include_pre' => $e['include_pre'] ?? null,
                            'include_post' => $e['include_post'] ?? null,
                            'list' => $e['form_list'] ?? null,
                            'help' => $e['form_help'] ?? null,
                            'placeholder' => $e['form_placeholder'] ?? null,
                        ]
                    ]; }),
            ]);
    }

    /**
     * Update settings
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function update(Request $request) {

        // Validate
        $request->validate(
            collect($this->getSettings())
                ->filter(function($f){ 
                    return isset($f['form_validate']);
                })
                ->mapWithKeys(function($f, $k) {
                    $rules = is_callable($f['form_validate']) ? $f['form_validate']() : $f['form_validate'];
                    return [Str::slug($k) => $rules];
                })
                ->toArray()
        );

        // Update
        foreach($this->getSettings() as $field_key => $field) {
            $value = $request->{Str::slug($field_key)};
            if ($value !== null) {
                if (isset($field['setter']) && is_callable($field['setter'])) {
                    $value = $field['setter']($value);
                }
                Setting::set($field_key, $value);
            } else {
                Setting::forget($field_key);
            }
        }
        Setting::save();
        
        // Redirect
        return redirect()->route($this->getRedirectRouteName())
            ->with('success', __('app.settings_updated'));
    }
}
