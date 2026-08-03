<?php
$db = new mysqli('localhost', 'root', '', 'ultimate_school');

$cols = array(
    'npsn_sd' => "VARCHAR(30) DEFAULT '10100001'",
    'npsn_smp' => "VARCHAR(30) DEFAULT '20200002'",
    'npsn_sma' => "VARCHAR(30) DEFAULT '30300003'",
    'kepala_sd' => "VARCHAR(150) DEFAULT 'Siti Rahmawati, S.Pd (Kepala SD)'",
    'kepala_smp' => "VARCHAR(150) DEFAULT 'Drs. H. Mulyadi, M.Pd (Kepala SMP)'",
    'kepala_sma' => "VARCHAR(150) DEFAULT 'Dr. Budi Santoso, M.Si (Kepala SMA)'"
);

foreach ($cols as $col => $def) {
    $c = $db->query("SHOW COLUMNS FROM sekolah LIKE '{$col}'");
    if ($c->num_rows === 0) {
        $db->query("ALTER TABLE sekolah ADD COLUMN {$col} {$def}");
        echo "Added column {$col} to table sekolah.\n";
    } else {
        echo "Column {$col} already exists in table sekolah.\n";
    }
}

$db->close();
