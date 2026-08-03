<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('verify_turnstile')) {
    function verify_turnstile($token) {
        if (empty($token)) {
            return false;
        }

        $secret_key = '0x4AAAAAACAcYp0o3lrsU4m_WPdtIh-bPMk';
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = array(
            'secret'   => $secret_key,
            'response' => $token,
            'remoteip' => $ip
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return isset($result['success']) && $result['success'] === true;
        }

        return false;
    }
}

if (!function_exists('render_turnstile')) {
    function render_turnstile() {
        $site_key = '0x4AAAAAACAcYu88og6fN1dH';
        return '<div class="cf-turnstile my-3 d-flex justify-content-center" data-sitekey="' . $site_key . '"></div>';
    }
}
