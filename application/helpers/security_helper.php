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

if (!function_exists('parse_kelas_number')) {
    function parse_kelas_number($nama_kelas) {
        if (empty($nama_kelas)) return 0;
        $str = strtoupper(trim($nama_kelas));
        
        // Roman numerals XII, XI, X, IX, VIII, VII, VI, V, IV, III, II, I
        if (preg_match('/\bXII\b/', $str)) return 12;
        if (preg_match('/\bXI\b/', $str)) return 11;
        if (preg_match('/\bX\b/', $str)) return 10;
        if (preg_match('/\bIX\b/', $str)) return 9;
        if (preg_match('/\bVIII\b/', $str)) return 8;
        if (preg_match('/\bVII\b/', $str)) return 7;
        if (preg_match('/\bVI\b/', $str)) return 6;
        if (preg_match('/\bV\b/', $str)) return 5;
        if (preg_match('/\bIV\b/', $str)) return 4;
        if (preg_match('/\bIII\b/', $str)) return 3;
        if (preg_match('/\bII\b/', $str)) return 2;
        if (preg_match('/\bI\b/', $str)) return 1;
        
        if (preg_match('/\b(1[0-2]|[1-9])\b/', $str, $m)) return (int)$m[1];
        if (preg_match('/\d+/', $str, $m)) return (int)$m[0];
        
        return 0;
    }
}
