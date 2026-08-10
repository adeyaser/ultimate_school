<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('encrypt_id')) {
    function encrypt_id($id) {
        if (empty($id)) return '';
        if (!is_numeric($id)) return $id;
        
        $salt = 'ULTIMATE_SCHOOL_SECRET_ID_SALT_2026';
        $hash = substr(md5($salt . $id), 0, 6);
        $data = $id . '|' . $hash;
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('decrypt_id')) {
    function decrypt_id($encrypted) {
        if (empty($encrypted)) return 0;
        if (is_numeric($encrypted)) return (int)$encrypted;
        
        $salt = 'ULTIMATE_SCHOOL_SECRET_ID_SALT_2026';
        $base64 = strtr($encrypted, '-_', '+/');
        $mod4 = strlen($base64) % 4;
        if ($mod4) {
            $base64 .= substr('====', $mod4);
        }
        
        $decoded = base64_decode($base64, true);
        if ($decoded === false || strpos($decoded, '|') === false) return 0;
        
        $parts = explode('|', $decoded);
        if (count($parts) !== 2) return 0;

        list($id, $hash) = $parts;
        $expected_hash = substr(md5($salt . $id), 0, 6);
        if (hash_equals($expected_hash, $hash)) {
            return (int)$id;
        }
        return 0;
    }
}
