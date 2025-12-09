<?php
include("header.php");
include("koperasiQry.php");

echo "<div class='container mt-4'>";
echo "<h3>Debug Session Information</h3>";
echo "<div class='card'>";
echo "<div class='card-body'>";

echo "<h5>Session Variables:</h5>";
echo "<table class='table table-bordered'>";
echo "<tr><th>Session Key</th><th>Value</th></tr>";

// Coba berbagai kombinasi session key
$sessionKeys = [
    'Cookie_userID',
    'Cookie_UserID',
    'Cookie_userName',
    'Cookie_UserName',
    'UserID',
    'userID',
    'UserName',
    'userName',
    'Cookie_groupID'
];

foreach ($sessionKeys as $key) {
    $value = get_session($key);
    $display = empty($value) ? '<span class="text-danger">KOSONG</span>' : '<span class="text-success">' . htmlspecialchars($value) . '</span>';
    echo "<tr><td><strong>$key</strong></td><td>$display</td></tr>";
}

echo "</table>";

echo "<h5 class='mt-4'>Raw $_SESSION:</h5>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "</div>";
echo "</div>";
echo "</div>";

include("footer.php");
?>
