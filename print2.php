<?php

include_once('config.php');
// routeros api
include_once('lib/routeros_api.class.php');
include_once('lib/formatbytesbites.php');
$API = new RouterosAPI();
$API->debug = false;
$API->connect($iphost, $userhost, $passwdhost);

// var_dump($API);exit;
$getidentity = $API->comm("/system/identity/print");
$identity = $getidentity[0]['name'];
// $srv = $_GET['srv'];
// $prof = 'unl-6jam-2000';
// $comm = isset($_GET['comment']) ? $_GET['comment'] : null;
$hotspotuser = $_GET['user'];
$small = 'yes';
$usermode = 'vc';
$getuser = $API->comm("/ip/hotspot/user/print", array(
    "?name" => "$hotspotuser",
  ));
//   var_dump($getuser);exit;
$TotalReg = count($getuser);
$getuprofile = $getuser[0]['profile'];


$getprofile = $API->comm("/ip/hotspot/user/profile/print", array("?name" => "$getuprofile"));
$getsharedu = $getprofile[0]['shared-users'];
$ponlogin = $getprofile[0]['on-login'];
// $validity = ;
$validityangka = substr(explode(",", $ponlogin)[3], -2, 1);
$validityhuruf = substr(explode(",", $ponlogin)[3], -1) == 'd' ? 'Hari' : 'd';
$validity = $validityangka.' '.$validityhuruf;
$getsprice = explode(",", $ponlogin)[4];
$price = "Rp. " . number_format((float)$getsprice, 0, ",", ".");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Voucher-<?= $hotspotname . "-" . $getuprofile . "-" . $id; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="pragma" content="no-cache" />
		<!-- <link rel="icon" href="../img/favicon.png" /> -->
		<!-- <script src="../js/qrious.min.js"></script> -->
		<style>
body {
  color: #000000;
  background-color: #FFFFFF;
  font-size: 14px;
  font-family:  'Helvetica', arial, sans-serif;
  margin: 0px;
  -webkit-print-color-adjust: exact;
}
table.voucher {
  display: inline-block;
  border: 2px solid black;
  margin: 2px;
}
@page
{
  size: auto;
  margin-left: 7mm;
  margin-right: 3mm;
  margin-top: 9mm;
  margin-bottom: 3mm;
}
@media print
{
  table { page-break-after:auto }
  tr    { page-break-inside:avoid; page-break-after:auto }
  td    { page-break-inside:avoid; page-break-after:auto }
  thead { display:table-header-group }
  tfoot { display:table-footer-group }
}
#num {
  float:right;
  display:inline-block;
}
.qrc {
  width:30px;
  height:30px;
  margin-top:1px;
}
		</style>
	</head>
	<body>

<?php
// for ($i = 0; $i < $TotalReg; $i++) {;
    foreach($getuser as $regtable ){
//   $regtable = $getuser[$i];
  $uid = str_replace("=","",base64_encode($regtable['.id']));
  $idqr = str_replace("=","",base64_encode(($regtable['.id']."qr")));
  $username = $regtable['name'];
  $password = $regtable['password'];
  $profile = $regtable['profile'];
  $timeangka = substr($regtable['limit-uptime'], -2, 1);
  $timehuruf = substr($regtable['limit-uptime'], -1) == 'h' ? 'Jam' : 'h';
  $timelimit = $timeangka.' '.$timehuruf;
  
  
  $comment = $regtable['comment'];

  
//   $urilogin = "http://$dnsname/login?username=$username&password=$password";
//   $qrcode = "
// 	<canvas class='qrcode' id='".$uid."'></canvas>
//     <script>
//       (function() {
//         var ".$uid." = new QRious({
//           element: document.getElementById('".$uid."'),
//           value: '".$urilogin."',
//           size:'256'
//         });

//       })();
//     </script>
// 	";
 
  $num = $i + 1;
  ?>
<?php

  if ($small == "yes") {
    include('template-small2.php');
  }
?>
<?php 
} ?>
<br>
<br>
<br>
<br>
<a style="padding-left: 30px; font-size:30px" href="./?comment=<?=$comment?>"><== KEMBALI</a>
	
</body>
</html>
