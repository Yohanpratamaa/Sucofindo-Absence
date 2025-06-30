<?php
// Simple test script untuk format number
$testValues = [1500000, 15000000, 150000000, 500000];

echo "<h1>Test Format Number</h1>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Input</th><th>PHP number_format</th><th>JavaScript toLocaleString</th></tr>";

foreach($testValues as $value) {
    $phpFormat = number_format($value, 0, ',', '.');
    echo "<tr>";
    echo "<td>" . $value . "</td>";
    echo "<td>Rp" . $phpFormat . "</td>";
    echo "<td id='js-" . $value . "'></td>";
    echo "</tr>";
}

echo "</table>";

echo "<script>";
echo "const values = " . json_encode($testValues) . ";";
echo "values.forEach(value => {";
echo "  const formatted = 'Rp' + parseInt(value).toLocaleString('id-ID');";
echo "  document.getElementById('js-' + value).innerHTML = formatted;";
echo "});";
echo "</script>";
?>
