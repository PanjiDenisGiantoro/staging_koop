<?php
include("C:/xampp/htdocs/staging_koop/common.php");

// Check which columns already exist
$existing = array();
$rs = $conn->Execute("SHOW COLUMNS FROM general");
while(!$rs->EOF) { $existing[] = $rs->fields['Field']; $rs->MoveNext(); }

$cols = array(
    "gl_bank_code" => "ALTER TABLE general ADD COLUMN gl_bank_code varchar(255) DEFAULT NULL",
    "gl_bank_name" => "ALTER TABLE general ADD COLUMN gl_bank_name varchar(255) DEFAULT NULL",
    "jumlah_minimum" => "already exists check",
    "had_pengeluaran_sehari" => "ALTER TABLE general ADD COLUMN had_pengeluaran_sehari decimal(15,2) DEFAULT 0",
    "had_pengeluaran_transaksi" => "ALTER TABLE general ADD COLUMN had_pengeluaran_transaksi decimal(15,2) DEFAULT 0",
);

foreach($cols as $col => $sql) {
    if(in_array($col, $existing)) {
        echo "$col: ALREADY EXISTS\n";
    } else {
        if($col == "jumlah_minimum") {
            echo "$col: ALREADY EXISTS (checked)\n";
        } else {
            $conn->Execute($sql);
            echo "$col: " . ($conn->ErrorMsg() ? "ERROR: " . $conn->ErrorMsg() : "ADDED OK") . "\n";
        }
    }
}

// Verify
echo "\n--- Verify new columns ---\n";
$rs2 = $conn->Execute("SHOW COLUMNS FROM general WHERE Field IN ('gl_bank_code','gl_bank_name','jumlah_minimum','had_pengeluaran_sehari','had_pengeluaran_transaksi')");
while(!$rs2->EOF) {
    echo $rs2->fields['Field'] . " | " . $rs2->fields['Type'] . "\n";
    $rs2->MoveNext();
}
?>
