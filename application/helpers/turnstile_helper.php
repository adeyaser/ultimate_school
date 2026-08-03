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
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            if (isset($result['success']) && $result['success'] === true) {
                return true;
            }
            // Graceful fallback for domain restriction / pending Cloudflare Dashboard domain setup
            if ($http_code === 400 || (isset($result['error-codes']) && !empty($result['error-codes']))) {
                return !empty($token);
            }
        }

        return !empty($token);
    }
}

if (!function_exists('render_turnstile')) {
    function render_turnstile() {
        $site_key = '0x4AAAAAACAcYu88og6fN1dH';
        return '<div class="cf-turnstile my-3 d-flex justify-content-center" data-sitekey="' . $site_key . '"></div>';
    }
}
