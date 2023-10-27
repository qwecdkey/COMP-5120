<?php
require "database.php";
$con = get_connection();
if (!$con) {
    report_error(mysqli_error($con));
    die();
}

function report_error($msg) {
  echo '<div style="width: 100%; background: #f2dede; padding: 10px; border-radius: 5px">' . $msg . '</div>';
}

function reportExplain($msg) {

  echo '<div style="width: 100%; background: ##f5f5f5; padding: 10px; border-radius: 5px">' . $msg . '</div>';
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>DataBase Term Project</title>
  <link rel="stylesheet" href="style.css" type="text/css" media="all" />
</head>

<body>
    <h1 style="margin-bottom: 0;">Database Query Input</h1><h2>Qingtao Lu(qzl0037@auburn.edu)</h2>

<div style="margin: 5px">
  <form method="POST" action="index.php">
    <textarea id="query" name="query" style="font-family: consolas; font-size: larger; width: 100%; height: 150px; border: 1px solid gainsboro; padding: 5px"><?= stripslashes($_POST["query"])?></textarea>
    <br />
    <input type="submit"/> <button type="button" onclick="document.getElementById('query').value = ''";>Clear</button>
  </form>
</div>

<div style="padding-top: 15px">

<?php
  if (isset($_POST["query"])) {
    $query = stripcslashes($_POST["query"]);
    $q = strtolower($query);
    $forbidden = array("drop");
    foreach($forbidden as $key) {
      if(strpos($q, $key) !== false) {
        report_error("DROP is not allowed.");
        die();
      }
    }
    
    $explainedKeys = array('create', 'insert', 'delete', 'update');
    foreach($explainedKeys as $key) {
      if(strpos($q, $key) !== false) {
        switch($key){
          case 'create':
             reportExplain("Table created.");
            break;
          case 'insert':
             reportExplain("Row inserted.");
            break;
          case 'delete':
             reportExplain("Row deleted.");
            break;
          case 'update':
             reportExplain("Table updated.");
            break;
          default:
             reportExplain("This is the result:");
            break;
        }
      }
    }

    if ($query !== "") {
      $result = execute_query($con, $query);
      if ($result == false) {
        report_error(mysqli_error($con));
        die();
      }

      ?>
      <table class="bordered">
        <thead>
        <?php
        $numFields = mysqli_num_fields($result);

        echo "<tr>";
        for($i = 0; $i < $numFields; $i++) {
          $field = mysqli_fetch_field_direct($result, $i);
          echo "<th>" . $field->name . "</th>";
        }
        echo "</tr>";
        ?>
        </thead>

        <?php
        $rows = array();
        while($resultRow = mysqli_fetch_assoc($result)) {
          $rows[] = $resultRow;
        }
        foreach($rows as $row) {
          echo "<tr>";
          foreach($row as $col) {
            echo "<td>" . $col . "</td>";
          }
          echo "</tr>";
        }

        mysqli_free_result($result);
      }
      ?>
    </table>
  <?php
  }
?>
</div>

</body>
</html>
<?php mysqli_close($con); ?>