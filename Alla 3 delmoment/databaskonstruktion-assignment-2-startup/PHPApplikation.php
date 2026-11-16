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
$pdo = new PDO('mysql:host=mysql;port=3306;dbname=a25milha;charset=utf8', 'dbkonstruktion', '<REDACTED>');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Sokning och filtrering av personer
$soknamn = isset($_POST['soknamn']) ? $_POST['soknamn'] : "";
$filter_kod = isset($_POST['filter_kod']) ? $_POST['filter_kod'] : "";
$kodlista = $pdo->query("SELECT DISTINCT Kodnamn FROM Person WHERE Kodnamn IS NOT NULL ORDER BY Kodnamn ASC");
$sql = "SELECT PersonId, Namn, Kodnamn FROM Person WHERE Namn LIKE ?";
if ($filter_kod === "SAKNAS") {
  $sql .= " AND Kodnamn IS NULL";
} elseif ($filter_kod !== "") {
  $sql .= " AND Kodnamn = ?";
}
$sql .= " ORDER BY Namn ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, "%" . $soknamn . "%");
if ($filter_kod !== "" && $filter_kod !== "SAKNAS") {
  $stmt->bindValue(2, $filter_kod);
}
$stmt->execute();

$platslista = $pdo->query("SELECT PlatsKod, PlatsNamn FROM Plats ORDER BY PlatsKod");
$gradlista = $pdo->query("SELECT GradKod, Beskrivning FROM GradBeskrivning ORDER BY GradKod");
$handlista = $pdo->query("SELECT h.AgentNamn, h.AgentNr, a.Fornamn, a.Efternamn FROM Handlaggare h JOIN Agent a ON a.AgentNamn = h.AgentNamn AND a.AgentNr = h.AgentNr ORDER BY h.AgentNamn, h.AgentNr");
$obslista = $pdo->query("SELECT ObsId, IncidentNamn FROM Observation ORDER BY ObsId ASC");

$med_reg = "";
$med_ark = "";
$med_upd = "";

// Registrera ny observation med INSERT
if (isset($_POST['registrera'])) {
  $ny_obsid = trim($_POST['ny_obsid']);
  $ny_datum = str_replace("T", " ", trim($_POST['ny_datum']));
  if (strlen($ny_datum) === 16) $ny_datum .= ":00";
  $ny_grad = trim($_POST['ny_grad']);
  $ny_sakerhet = trim($_POST['ny_sakerhet']);
  $ny_incidentid = trim($_POST['ny_incidentid']);
  $ny_incidentnamn = trim($_POST['ny_incidentnamn']);
  $ny_plats = trim($_POST['ny_plats']);
  $ny_hand = explode("|", trim($_POST['ny_handlaggare']));
  if ($ny_obsid === "" || $ny_datum === "" || $ny_incidentid === "" || $ny_incidentnamn === "" || count($ny_hand) !== 2) {
    $med_reg = "Fyll i alla falt.";
  } elseif (!ctype_digit($ny_obsid) || !ctype_digit($ny_incidentid)) {
    $med_reg = "ObsId och IncidentId maste vara heltal.";
  } elseif (!ctype_digit($ny_grad) || (int)$ny_grad < 1 || (int)$ny_grad > 4) {
    $med_reg = "Grad maste vara mellan 1 och 4.";
  } elseif (!is_numeric($ny_sakerhet) || (float)$ny_sakerhet < 1 || (float)$ny_sakerhet > 114) {
    $med_reg = "Sakerhet maste vara mellan 1 och 114.";
  } else {
    try {
      $ins = $pdo->prepare("INSERT INTO Observation (ObsId, Datum, GradKod, Sakerhet, IncidentId, IncidentNamn, PlatsKod, RegistreradAvNamn, RegistreradAvNr) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $ins->execute([$ny_obsid, $ny_datum, $ny_grad, $ny_sakerhet, $ny_incidentid, $ny_incidentnamn, $ny_plats, $ny_hand[0], $ny_hand[1]]);
      $med_reg = "Observation " . $ny_obsid . " registrerad.";
    } catch (PDOException $e) {
      $med_reg = "Kunde inte registrera observationen.";
    }
  }
}

// Arkivera observation med proceduren ARKIVERAOBSERVATION
if (isset($_POST['arkivera'])) {
  $ark_obsid = trim($_POST['arkiv_obsid']);
  if ($ark_obsid === "" || !ctype_digit($ark_obsid)) {
    $med_ark = "Valj en observation att arkivera.";
  } else {
    try {
      $ark = $pdo->prepare("CALL ARKIVERAOBSERVATION(?)");
      $ark->bindValue(1, $ark_obsid);
      $ark->execute();
      $ark->closeCursor();
      $med_ark = "Observation " . $ark_obsid . " arkiverad.";
    } catch (PDOException $e) {
      $med_ark = "Kunde inte arkivera observationen.";
    }
  }
}

// Uppdatera sakerhet for en observation med UPDATE
if (isset($_POST['uppdatera'])) {
  $upd_obsid = trim($_POST['upd_obsid']);
  $upd_sakerhet = trim($_POST['upd_sakerhet']);
  if ($upd_obsid === "" || !ctype_digit($upd_obsid)) {
    $med_upd = "Valj en observation att uppdatera.";
  } elseif (!is_numeric($upd_sakerhet) || (float)$upd_sakerhet < 1 || (float)$upd_sakerhet > 114) {
    $med_upd = "Sakerhet maste vara mellan 1 och 114.";
  } else {
    $upd = $pdo->prepare("UPDATE Observation SET Sakerhet = ? WHERE ObsId = ?");
    $upd->bindValue(1, $upd_sakerhet);
    $upd->bindValue(2, $upd_obsid);
    $upd->execute();
    $med_upd = "Observation " . $upd_obsid . " uppdaterad.";
  }
}
?>


<h2>Sok personer</h2>
<form action="PHPApplikation.php" method="POST">
  Namn: <input type="text" name="soknamn" /><br>
  <input type="submit" name="sok" value="Sok" />
</form>


<h2>Filtrera personer</h2>
<form action="PHPApplikation.php" method="POST">
  Kodnamn:
  <select name="filter_kod">
    <option value="">Alla</option>
    <?php
    foreach ($kodlista as $k) {
      echo "<option value='" . $k['Kodnamn'] . "'";
      if ($filter_kod === $k['Kodnamn']) echo " selected";
      echo ">" . $k['Kodnamn'] . "</option>";
    }
    ?>
    <option value="SAKNAS"<?php if ($filter_kod === "SAKNAS") echo " selected"; ?>>Saknar kodnamn</option>
  </select><br>
  <input type="submit" name="filtrera" value="Filtrera" />
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
<p>
<?php
$stat = $pdo->query("CALL GETAVGSAKERHET()");
$rad = $stat->fetch(PDO::FETCH_ASSOC);
echo "Medelsakerhet: " . $rad['MedelSakerhet'] . " %";
$stat->closeCursor();
?>
</p>

<h2>3. Observationer med vittnen (Radlayout)</h2>
<table border="1">
<tr>
<th>ObsId</th>
<th>Datum</th>
<th>Grad</th>
<th>Säkerhet</th>
<th>Incident</th>
<th>Plats</th>
<th>Handläggare</th>
<th>Vittnen / Rapportörer</th>
</tr>
<?php
// Radlayout med jämförelsevariabel enligt lärarens kodexempel
$prevObs = "NONE";
$sql = "SELECT O.ObsId, O.Datum, O.GradKod, O.Sakerhet, O.IncidentId, O.IncidentNamn, PL.PlatsNamn, O.RegistreradAvNamn, O.RegistreradAvNr, P.PersonId, P.Namn, P.Kodnamn FROM Observation O LEFT JOIN Rapporterat R ON O.ObsId = R.ObsId LEFT JOIN Person P ON R.PersonId = P.PersonId JOIN Plats PL ON PL.PlatsKod = O.PlatsKod ORDER BY O.ObsId ASC;";

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
        echo "<td>" . $row['IncidentId'] . " / " . $row['IncidentNamn'] . "</td>";
        echo "<td>" . $row['PlatsNamn'] . "</td>";
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

<h2>Registrera ny observation (INSERT)</h2>
<?php if ($med_reg !== "") echo "<p>" . $med_reg . "</p>"; ?>
<form action="PHPApplikation.php" method="POST">
  ObsId: <input type="text" name="ny_obsid" /><br>
  Datum: <input type="datetime-local" name="ny_datum" /><br>
  Grad:
  <select name="ny_grad">
    <?php
    foreach ($gradlista as $g) {
      echo "<option value='" . $g['GradKod'] . "'>" . $g['GradKod'] . " - " . $g['Beskrivning'] . "</option>";
    }
    ?>
  </select><br>
  Sakerhet (1-114): <input type="text" name="ny_sakerhet" /><br>
  IncidentId: <input type="text" name="ny_incidentid" /><br>
  IncidentNamn: <input type="text" name="ny_incidentnamn" /><br>
  Plats:
  <select name="ny_plats">
    <?php
    foreach ($platslista as $p) {
      echo "<option value='" . $p['PlatsKod'] . "'>" . $p['PlatsNamn'] . "</option>";
    }
    ?>
  </select><br>
  Handlaggare:
  <select name="ny_handlaggare">
    <?php
    foreach ($handlista as $h) {
      echo "<option value='" . $h['AgentNamn'] . "|" . $h['AgentNr'] . "'>" . $h['AgentNamn'] . " " . $h['AgentNr'] . " (" . $h['Fornamn'] . " " . $h['Efternamn'] . ")</option>";
    }
    ?>
  </select><br>
  <input type="submit" name="registrera" value="Registrera" />
</form>

<h2>Arkivera observation (Procedur)</h2>
<?php if ($med_ark !== "") echo "<p>" . $med_ark . "</p>"; ?>
<form action="PHPApplikation.php" method="POST">
  Observation:
  <select name="arkiv_obsid">
    <?php
    foreach ($obslista as $o) {
      echo "<option value='" . $o['ObsId'] . "'>" . $o['ObsId'] . " / " . $o['IncidentNamn'] . "</option>";
    }
    ?>
  </select><br>
  <input type="submit" name="arkivera" value="Arkivera" />
</form>

<h2>Uppdatera sakerhet (UPDATE)</h2>
<?php if ($med_upd !== "") echo "<p>" . $med_upd . "</p>"; ?>
<form action="PHPApplikation.php" method="POST">
  Observation:
  <select name="upd_obsid">
    <?php
    $obslista2 = $pdo->query("SELECT ObsId, IncidentNamn FROM Observation ORDER BY ObsId ASC");
    foreach ($obslista2 as $o) {
      echo "<option value='" . $o['ObsId'] . "'>" . $o['ObsId'] . " / " . $o['IncidentNamn'] . "</option>";
    }
    ?>
  </select><br>
  Ny sakerhet (1-114): <input type="text" name="upd_sakerhet" /><br>
  <input type="submit" name="uppdatera" value="Uppdatera" />
</form>
</body>
</html>
