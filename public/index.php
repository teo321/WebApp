<?php
require "appointment.php";
require 'config/config.php';

$start = strtotime("+".APPO_MIN." day");
$end = strtotime("+".APPO_MAX." day");
$booked = $_APPO->get(date("Y-m-d", $start), date("Y-m-d", $end));

if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else {
	header("Location: register.php");
}

?>
 <link rel="stylesheet" type="text/css" href="select.css">
 <script src="select.js"></script>
 <a href="includes/logout.php">
				<i>logout</i>
</a>
<!--  SELECT APPOINTMENT DATE/SLOT -->
<h2>SELECT A DATE</h2>
<table id="select">
  <!-- FIRST ROW : HEADER CELLS -->
  <tr>
    <th></th>
    <?php foreach ($APPO_SLOTS as $slot) { echo "<th>$slot</th>"; } ?>
  </tr>
 
  <!--  FOLLOWING ROWS : DAYS -->
  <?php
  for ($unix=$start; $unix<=$end; $unix+=86400) {
    $thisDate = date("Y-m-d", $unix);
    $isDayOfWeek=date('N', strtotime($thisDate));
    if ($isDayOfWeek >= 1 && $isDayOfWeek <= 5)
    {
    echo "<tr><th>$thisDate</th>";
    
    $ct = 0;
      while($ct < count($APPO_SLOTS))
      {
        $aux=$ct;
        if($ct<count($APPO_SLOTS)-2)$aux=$ct+2;
        else if ($ct<count($APPO_SLOTS)-1) $aux=$ct+1;
        $slotAux = $APPO_SLOTS[$aux];
        $slotAux1 = $APPO_SLOTS[$aux-1];
        $slot=$APPO_SLOTS[$ct];
        
        if (isset($booked[$thisDate][$slot]) && ($ct===count($APPO_SLOTS)-2 || $ct===7) )
          {
            echo "<td class='booked'>Booked</td>";
            echo "<td class='booked'>Booked</td>";
            $ct++;
          }
        else if (isset($booked[$thisDate][$slot])) {
            echo "<td class='booked'>Booked</td>";
            echo "<td class='booked'>Booked</td>";
            echo "<td class='booked'>Rest</td>";
          $ct++;
          $ct++;
        } 
        else if($ct === count($APPO_SLOTS)-1 || isset($booked[$thisDate][$slotAux1]) || $ct===9 || $ct===8 ) 
            echo "<td></td>";
        else if(isset($booked[$thisDate][$slotAux])) {
            echo "<td></td>";
            echo "<td></td>";
          $ct++;
        }
        else {
            echo "<td onclick=\"select(this, '$thisDate', '$slot')\"></td>";
        }
        $ct++;
      
      }
    echo "</tr>";
  }
}
  ?>
</table>
 
<!--  CONFIRM -->
<h2>CONFIRM</h2>
<form id="confirm" method="post" action="book.php">
  <input type="hidden" name="user" value=<?php echo $_SESSION['username']?> >
  <input type="text" id="cdate" name="date" readonly placeholder="Select a time slot above">
  <input type="text" id="cslot" name="slot" readonly>
  <input type="submit" id="cgo" value="Go" disabled>
</form>
<?php


$sql="SELECT * FROM appointments";
$result = mysqli_query($con,$sql);

echo "<table id="."table"." >
<tr>
<th>Username</th>
<th>Date</th>
<th>Hour</th>
</tr>";
while($row = mysqli_fetch_array($result)) {
  echo "<tr>";
  echo "<td>" . $row['user_id'] . "</td>";
  echo "<td>" . $row['appo_date'] . "</td>";
  echo "<td>" . $row['appo_slot'] . "</td>";
  echo "</tr>";
}
echo "</table>";
?>