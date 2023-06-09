<?php
session_start();
if(isset($_SESSION['password'])) {
// ----------------------------------CONTENT HERE---------------------------------- //

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
$comm = isset($_GET['comment']) ? $_GET['comment'] : null;

if ($prof == "all") {
  $getuser = $API->comm("/ip/hotspot/user/print");
  $TotalReg = count($getuser);

  $counttuser = $API->comm("/ip/hotspot/user/print", array(
    "count-only" => ""
  ));
} elseif ($prof != "all") {
  $getuser = $API->comm("/ip/hotspot/user/print", array(
    "?profile" => "$prof",
  ));
  $TotalReg = count($getuser);

  $counttuser = $API->comm("/ip/hotspot/user/print", array(
    "count-only" => "",
    "?profile" => "$prof",
  ));
}
if ($comm != "") {
  $getuser = $API->comm("/ip/hotspot/user/print", array(
    "?comment" => "$comm",
    //"?uptime" => "00:00:00"
  ));
  $TotalReg = count($getuser);

  $counttuser = $API->comm("/ip/hotspot/user/print", array(
    "count-only" => "",
    "?comment" => "$comm",
  ));
}
//   $exp = $_GET['exp'];
//   if ($exp != "") {
//     $getuser = $API->comm("/ip/hotspot/user/print", array(
//       "?limit-uptime" => "1s",
//     ));

//     $counttuser = $API->comm("/ip/hotspot/user/print", array(
//       "count-only" => "",
//       "?limit-uptime" => "1s",
//     ));

//   }
$getprofile = $API->comm("/ip/hotspot/user/profile/print");
$TotalReg2 = count($getprofile);

?>
<html lang="en">

<head>
  <title>NaWa.NET</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>


<div class="container text-center">
  <h2 class="text-center">Daftar Voucher NaWa.NET</h2>
  <div class="form-group text-center">
    <label class="col-sm-2 control-label">Pilih Data Voucher :</label>
    <div class="col-sm-3">
      <select class="form-control" id="comment" name="comment" onchange="location = './?comment='+ this.value;">
        <?php
        if ($comm != "") {
          echo "<option value=''>Tampilkan User Online</option>";
        } else {
          echo "<option value=''>Pilih Voucher User</option>";
        }
        $TotalReg = count($getuser);
        $acomment = '';
        for ($i = 0; $i < $TotalReg; $i++) {
          $ucomment = $getuser[$i]['comment'];
          $uprofile = $getuser[$i]['profile'];
          $acomment .= "," . $ucomment . "#" . $uprofile;
        }

        $ocomment =  explode(",", $acomment);
        $comments = array_count_values($ocomment);
        foreach ($comments as $tcomment => $value) {
          $namacoment = explode("#", $tcomment)[0];
          $select = $comm == $namacoment ? 'selected' : '';
          if (is_numeric(substr($tcomment, 3, 3))) {
            echo "<option $select value='" . explode("#", $tcomment)[0] . "' >" . explode("#", $tcomment)[0] . " Sisa [" . $value . "]</option>";
          }
        }
        ?>
      </select>
    </div>
    <?php
    if ($comm != "") {
    ?>
      <div>
        <a href="print.php?id=<?= $comm; ?>" target="_blank" class="btn btn-success">Print Voucher</a>
      </div>
    <?php
    }
    ?>
  </div>
</div>
<div class="container">
<?php

if(!empty($comm)){
?>
  <table id="tabel" class="table table-striped">
    <thead>
      <tr>
        <th>Kode Voucher</th>
        <th>Paket Voucher WiFi</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      for ($i = 0; $i < $TotalReg; $i++) {
        $userdetails = $getuser[$i];
        $uname = $userdetails['name'];
        $uprofile = $userdetails['profile'];
        $uuptime = formatDTM($userdetails['uptime']);
        $ucomment = $userdetails['comment'];
        $ubytesi = $userdetails['bytes-in'];
      ?>

        <tr>
          <td><?= $uname; ?></td>
          <td><?= $uprofile; ?></td>
          <td><?= trim($uuptime) == '00:00:00' || $ubytesi == '0' ? '<a href="./print2.php?user='.$uname.'" class="btn btn-danger">Belum Terpakai</a>' : '<button class="btn btn-success">Sudah Terpakai</button>'; ?></td>

        </tr>


      <?php
      }
      ?>
    </tbody>
  </table>
<?php
} else{
?>
<table id="tabel" class="table table-striped">
    <thead>
      <tr>
        <th>Kode Voucher</th>
        <th>Masa Aktif</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php

$gethotspotactive = $API->comm("/ip/hotspot/active/print");

foreach($gethotspotactive as $hotspotactive){
        $uname = $hotspotactive['user'];
        $usesstime = trim(formatDTM($hotspotactive['session-time-left']));
        $uuptime = formatDTM($hotspotactive['uptime']);
        $ucomment = $hotspotactive['comment'];
        $ubytesi = $hotspotactive['bytes-in'];
        if($usesstime == ""){
          $usesstime = $ucomment;
          
          // $usesstime = date("d-m-Y H:i:s", strtotime($usesstime));
        } else{

        $jam = explode(':', $usesstime)[0];
        $menit = explode(':', $usesstime)[1];
        $detik = explode(':', $usesstime)[2];
        $usesstime =  $jam.' Jam '.$menit.' Menit Lagi';
        $new_time = date("H:i", strtotime('+'.$jam.' hour +'.$menit.' minutes +'.$detik.' second'));
        $usesstime = $usesstime.' =>> Pukul '.$new_time.' WIB';
        }
       
      ?>

        <tr>
          <td><?= $uname; ?></td>
          <td><?= $usesstime; ?></td>
          <td><?= trim($uuptime) == '00:00:00' || $ubytesi == '0' ? '<button class="btn btn-danger">Belum Terpakai</button>' : '<button class="btn btn-success">ONLINE</button>'; ?></td>

        </tr>


      <?php
      }
      ?>
    </tbody>
  </table>



<?php
}

?>

</div>
<center>
<a href="logout.php" class="btn btn-danger">Keluar</a>
</center>
</body>
<script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap.min.js"></script>

<script>
  $(document).ready(function() {
    $('#tabel').DataTable();
  });
</script>

</html>
<?php
    // echo '<center><h1>Selamat Datang ;)</h1><br/><a href="./logout.php">Logout</a>';
    // ----------------------------------CONTENT HERE---------------------------------- //
    } else {
        echo '<script>window.location.replace("./login.php");</script>';
    }

?>