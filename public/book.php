<?php
require "appointment.php";
echo $_APPO->save ($_POST["date"], $_POST["slot"], $_POST["user"])
? "OK" : $_APPO->error;
