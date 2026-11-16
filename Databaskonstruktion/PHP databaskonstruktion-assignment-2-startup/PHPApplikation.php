<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>
      PUCKO - Handlaggarapplikation
    </title>
</head>
<body>


<h1>
  PUCKO - Handlaggarapplikation
</h1>


<?php
$pdo = new PDO('mysql:host=localhost;dbname=a25milha;charset=utf8', 'handlaggare_app', '<REDACTED>');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<pre>";
print_r($_POST);
echo "</pre>";

$stmt = $pdo->query("SELECT PersonId, Namn, Kodnamn FROM Person ORDER BY Namn ASC");
?>


<form action="PHPApplikation.php" method="POST">
  Field1: <input type="text" name="field1" /><br>
  Field2: <input type="text" name="field2" /><br>
   <input type="checkbox" name="Skicka" /><br>
  Skicka: <input type="submit" />
</form>


<h2>Personer</h2>
<table border="1">
<tr>
<th>PersonId</th>
<th>Namn</th>
<th>Kodnamn</th>
</tr>
<?php
foreach ($stmt as $row) {
  echo "<tr>";
  echo "<td>" . $row['PersonId'] . "</td>";
  echo "<td>" . $row['Namn'] . "</td>";
  echo "<td>" . $row['Kodnamn'] . "</td>";
  echo "</tr>";
}
?>
</table>

<h2>Statistik over Observationer (Lagrad procedur)</h2>
<pre>
<?php
// Anrop den lagrade proceduren GETAVGSAKERHET
$stat = $pdo->query("CALL GETAVGSAKERHET()");
foreach ($stat as $row) {
  print_r($row);
}
$stat->closeCursor();
?>
</pre>

<h2>3. Observationer med vittnen (Radlayout)</h2>
<table border="1">
<tr>
<th>ObsId</th>
<th>Datum</th>
<th>Grad</th>
<th>Säkerhet</th>
<th>Handläggare</th>
<th>Vittnen / Rapportörer</th>
</tr>
<?php
// Radlayout med jämförelsevariabel enligt lärarens kodexempel
$prevObs = "NONE";
$sql = "SELECT O.ObsId, O.Datum, O.GradKod, O.Sakerhet, O.RegistreradAvNamn, O.RegistreradAvNr, P.PersonId, P.Namn, P.Kodnamn FROM Observation O LEFT JOIN Rapporterat R ON O.ObsId = R.ObsId LEFT JOIN Person P ON R.PersonId = P.PersonId ORDER BY O.ObsId ASC;";

foreach ($pdo->query($sql) as $row) {
    if ($prevObs != $row['ObsId']) {
        if ($prevObs != "NONE") {
            echo "</table></td></tr>";
        }
        echo "<tr>";
        echo "<td>" . $row['ObsId'] . "</td>";
        echo "<td>" . $row['Datum'] . "</td>";
        echo "<td>" . $row['GradKod'] . "</td>";
        echo "<td>" . $row['Sakerhet'] . "%</td>";
        echo "<td>" . $row['RegistreradAvNamn'] . " " . $row['RegistreradAvNr'] . "</td>";
        echo "<td><table border='1'>";
        echo "<tr><th>PersonId</th><th>Namn</th><th>Kodnamn</th></tr>";
    }
    if ($row['PersonId'] != NULL) {
        echo "<tr>";
        echo "<td>" . $row['PersonId'] . "</td>";
        echo "<td>" . $row['Namn'] . "</td>";
        echo "<td>" . $row['Kodnamn'] . "</td>";
        echo "</tr>";
    }
    $prevObs = $row['ObsId'];
}
if ($prevObs != "NONE") {
    echo "</table></td></tr>";
}
?>
</table>
</body>
</html>
