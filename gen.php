<?php
include_once('config.php');

// routeros api
include_once('lib/routeros_api.class.php');
include_once('lib/formatbytesbites.php');
$API = new RouterosAPI();
$API->debug = false;
$API->connect($iphost, $userhost, $passwdhost);

//setup genret vouhcer
        $qty = 50;
		$server = 'all';
		$user = 'vc';
		$userl = 6;
		$prefix = '6j';
		$char = 'lower';
		$profile = 'unl-6jam-2000';
		$timelimit = '6h';
		$datalimit = '';
		$adcomment = 'MB-IDA';
        $mbgb = '';

        if ($timelimit == "") {
			$timelimit = "0";
		} else {
			$timelimit = $timelimit;
		}
		if ($datalimit == "") {
			$datalimit = "0";
		} else {
			$datalimit = $datalimit * $mbgb;
		}
		if ($adcomment == "") {
			$adcomment = "";
		} else {
			$adcomment = $adcomment;
		}
		$getprofile = $API->comm("/ip/hotspot/user/profile/print", array("?name" => "$profile"));
		$ponlogin = $getprofile[0]['on-login'];
		$getvalid = explode(",", $ponlogin)[3];
		$getprice = explode(",", $ponlogin)[2];
		$getsprice = explode(",", $ponlogin)[4];
		$getlock = explode(",", $ponlogin)[6];
		$commt = $user . "-" . rand(100, 999) . "-" . date("m.d.y") . "-" . $adcomment;
		/** 
       
            */
		$a = array("1" => "", "", 1, 2, 2, 3, 3, 4);

		// if ($user == "up") {
		// 	for ($i = 1; $i <= $qty; $i++) {
		// 		if ($char == "lower") {
		// 			$u[$i] = randLC($userl);
		// 		} elseif ($char == "upper") {
		// 			$u[$i] = randUC($userl);
		// 		} elseif ($char == "upplow") {
		// 			$u[$i] = randULC($userl);
		// 		} elseif ($char == "mix") {
		// 			$u[$i] = randNLC($userl);
		// 		} elseif ($char == "mix1") {
		// 			$u[$i] = randNUC($userl);
		// 		} elseif ($char == "mix2") {
		// 			$u[$i] = randNULC($userl);
		// 		}
		// 		if ($userl == 3) {
		// 			$p[$i] = randN(3);
		// 		} elseif ($userl == 4) {
		// 			$p[$i] = randN(4);
		// 		} elseif ($userl == 5) {
		// 			$p[$i] = randN(5);
		// 		} elseif ($userl == 6) {
		// 			$p[$i] = randN(6);
		// 		} elseif ($userl == 7) {
		// 			$p[$i] = randN(7);
		// 		} elseif ($userl == 8) {
		// 			$p[$i] = randN(8);
		// 		}

		// 		$u[$i] = "$prefix$u[$i]";
		// 	}

		// 	for ($i = 1; $i <= $qty; $i++) {
		// 		$API->comm("/ip/hotspot/user/add", array(
		// 			"server" => "$server",
		// 			"name" => "$u[$i]",
		// 			"password" => "$p[$i]",
		// 			"profile" => "$profile",
		// 			"limit-uptime" => "$timelimit",
		// 			"limit-bytes-total" => "$datalimit",
		// 			"comment" => "$commt",
		// 		));
		// 	}
		// }

		if ($user == "vc") {
			$shuf = ($userl - $a[$userl]);
			for ($i = 1; $i <= $qty; $i++) {
				if ($char == "lower") {
					$u[$i] = randLC($shuf);
				} elseif ($char == "upper") {
					$u[$i] = randUC($shuf);
				} elseif ($char == "upplow") {
					$u[$i] = randULC($shuf);
				}
				if ($userl == 3) {
					$p[$i] = randN(1);
				} elseif ($userl == 4 || $userl == 5) {
					$p[$i] = randN(2);
				} elseif ($userl == 6 || $userl == 7) {
					$p[$i] = randN(3);
				} elseif ($userl == 8) {
					$p[$i] = randN(4);
				}

				$u[$i] = "$prefix$u[$i]$p[$i]";

				if ($char == "num") {
					if ($userl == 3) {
						$p[$i] = randN(3);
					} elseif ($userl == 4) {
						$p[$i] = randN(4);
					} elseif ($userl == 5) {
						$p[$i] = randN(5);
					} elseif ($userl == 6) {
						$p[$i] = randN(6);
					} elseif ($userl == 7) {
						$p[$i] = randN(7);
					} elseif ($userl == 8) {
						$p[$i] = randN(8);
					}

					$u[$i] = "$prefix$p[$i]";
				}
				if ($char == "mix") {
					$p[$i] = randNLC($userl);


					$u[$i] = "$prefix$p[$i]";
				}
				if ($char == "mix1") {
					$p[$i] = randNUC($userl);


					$u[$i] = "$prefix$p[$i]";
				}
				if ($char == "mix2") {
					$p[$i] = randNULC($userl);


					$u[$i] = "$prefix$p[$i]";
				}

			}
			for ($i = 1; $i <= $qty; $i++) {
				$API->comm("/ip/hotspot/user/add", array(
					"server" => "$server",
					"name" => "$u[$i]",
					"password" => "$u[$i]",
					"profile" => "$profile",
					"limit-uptime" => "$timelimit",
					"limit-bytes-total" => "$datalimit",
					"comment" => "$commt",
				));
                echo $i.'.) '."<font color='green'>$u[$i]</font>".' OK<br>';
			}
		}


