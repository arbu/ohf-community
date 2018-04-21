@component('components.form.bsInput', [ 'name' => $name, 'label' => $label, 'help' => $help ?? __('app.year_month_day') ])
    {{ Form::text($name, $value, array_merge([ 'class' => 'form-control'.($errors->has($name) ? ' is-invalid' : ''), 'pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}', 'title' => __('app.yyyy_mm_dd'), 'placeholder' => __('app.yyyy_mm_dd') ], $attributes)) }}
@endcomponent
