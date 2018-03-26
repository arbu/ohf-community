<?php

if (! function_exists('form_id_string')) {
    function form_id_string($value, $suffix = null) {
        return trim(preg_replace('/[^A-Za-z0-9-_]+/', '-', $value . ($suffix != null ? '_' . $suffix : '')));
    }
}

if (! function_exists('email_url')) {
    function email_url($address) {
        return 'mailto:' . $address;
    }
}

if (! function_exists('phone_url')) {
    function phone_url($number) {
        return 'tel:' . $number;
    }
}

if (! function_exists('whatsapp_url')) {
    function whatsapp_url($number) {
        return 'whatsapp://send?phone=' . preg_replace('/^\+/', '', $number);
        // See https://faq.whatsapp.com/en/android/26000030/?category=5245251
        //return 'https://api.whatsapp.com/send?phone=' . preg_replace('/^\+/', '', $number);
    }
}

if (! function_exists('skype_call_url')) {
    function skype_call_url($name) {
        return 'skype:' . $name . '?call';
    }
}
