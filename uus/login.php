<?php
header ("Content-Type: text/html; charset=utf-8");

// Get user information obtained by Apache from the Estonian ID card.
// Return list [last_name,first_name,person_code] or False if fails.

function get_user () {
  // get relevant environment vars set by Apache
  // SSL_CLIENT_S_DN example:
  //  /C=EE/O=ESTEID/OU=authentication/CN=SMITH,JOHN,37504170511/
  //  SN=SMITH/GN=JOHN/serialNumber=37504170511
  $ident=getenv("SSL_CLIENT_S_DN");
  $verify=getenv("SSL_CLIENT_VERIFY");
  // echo $ident . "<br>";
  // echo $verify . "<br>";

  // check and parse the values
  if (!$ident || $verify!="SUCCESS") return False;
  $ident=certstr2utf8($ident); // old cards use UCS-2, new cards use UTF-8
  //if (strpos($ident,"/C=EE/O=ESTEID")!=0 && ) return False;
  $ps=strpos($ident,",SN=");
  $pg=strpos($ident,",GN=");
  $pc=strpos($ident,"serialNumber=");
  if (!$ps || !$pg) return False;
  $pse=strpos($ident,",",$ps+1);
  $pge=strpos($ident,",",$pg+1);
  $pce=strpos($ident,",",$pc+1);
  $res=array(substr($ident,$ps+4,$pse-($ps+4)),
             substr($ident,$pg+4,$pge-($pg+4)),
             substr($ident,$pc+13,11));
  return $res;
}

// Convert names from UCS-2/UTF-16 to UTF-8.
// Function taken from help.zone.eu.

function certstr2utf8 ($str) {
  $str = preg_replace ("/\\\\x([0-9ABCDEF]{1,2})/e", "chr(hexdec('\\1'))", $str);
  $result="";
  $encoding=mb_detect_encoding($str,"ASCII, UCS2, UTF8");
  if ($encoding=="ASCII") {
    $result=mb_convert_encoding($str, "UTF-8", "ASCII");
  } else {
    if (substr_count($str,chr(0))>0) {
      $result=mb_convert_encoding($str, "UTF-8", "UCS2");
    } else {
      $result=$str;
    }
  }
  return $result;
}

function user_exists ($db, $user) {
    $sql = "SELECT count(*) as c FROM person
            WHERE id_code = '". $user[2] ."'";
    $res = $db->query($sql);
    $row = $res->fetchArray(SQLITE3_ASSOC);
    return $row['c'] > 0;
}

function save_to_db ($db, $user) {
    $sql = "INSERT INTO person (name, id_code, u_id)
            VALUES ('". $user[1] . " " . $user[0] ."', '". $user[2] ."', '". uniqid() ."')";
    return $db->exec($sql);
}

function get_user_u_id($db, $user) {
    $sql = "SELECT u_id FROM person
            WHERE id_code = '". $user[2] . "'";
    $res = $db->query($sql);
    $row = $res->fetchArray(SQLITE3_ASSOC);
    return $row['u_id'];
}

function save_user_gg_data($db, $u_id, $json) {
    $obj = json_decode($json);
    $name = $obj->{'name'};
    $img_url = $obj->{'img_url'};
    $email = $obj->{'email'};
    $id = $obj->{'id'};

    $sql = "UPDATE person SET
            gg_name='". $name ."', gg_img='". $img_url ."', gg_id='". $id ."', email='". $email ."'
            WHERE u_id='". $u_id ."'";
    return $db->exec($sql);
}

//  Actual script to run

$user = get_user();

$db = new SQLite3('../data.db');
$old_user = True;
$user_u_id = "";

if ($user) {
    if ($db) {
        $old_user = user_exists($db, $user);
        if (!$old_user) {
            save_to_db($db ,$user);
        }
        $user_u_id = get_user_u_id($db, $user);
    } else {
        echo "Database ERROR";
    }
    // echo "Last name: " . $user[0] . "<br>";
    // echo "First name: " . $user[1] . "<br>";
    // echo "Person code: " . $user[2] . "<br>";
}

?>

<!doctype html>
<html>
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <meta name="google-signin-client_id"
        	content="107351278458-2ham2g0jg4ijs2noa9fsoanqul3kllva.apps.googleusercontent.com">

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

        <style>
            .data_container {
                padding: 10px;
            }

            #logoutbutton {
                position: absolute;
                right: 20px;
                top: 20px;
            }
        </style>

    </head>
    <body>
    <?php if (!$user): ?>
        <h1>Authentication failed!</h1>
    <?php else: ?>
        <script src="fblogin.js"></script>

        <div class="container text-center">

            <!-- <button id="logoutbutton" type="button" class="btn btn-default">Logout</button> -->

            <?php if ($old_user): ?>
                <h1>Welcome back, <?=$user[1]?>
            <?php else: ?>
                <h1>Hello, <?=$user[1]?>
            <?php endif; ?>

            <h3>Connect your social media accounts</h3>

            <!-- <div class="alert alert-success" role="alert">...</div> -->
            <!-- <div class="alert alert-danger" role="alert">...</div> -->

            <button id="fbbutton" type="button" class="btn btn-primary" onClick="fb_login()">Facebook</button>
            <button id="ggbutton" type="button" class="btn btn-danger" onclick="login()">Google</button>
            <br>
            <br>

            <h4>Your data</h4>
            <div class="data_container">
                <h5>ID-card</h6>
                <div id="id_data_container">
                    <div id='name'>Name</div>
                    <div id='id_code'>ID-code</div>
                </div>
            </div>
            <div class="data_container">
                <h5>Facebook</h6>
                <div id="fb_data_container">
                    <div id='picture'>Picture</div>
                    <div id='name'>Name</div>
                    <div id='id'>ID</div>
                    <div id='email'>Email</div>
                </div>
            </div>
            <div class="data_container">
                <h5>Google</h6>
                <div id="gg_data_container">
                    <div id='picture'>Picture</div>
                    <div id='name'>Name</div>
                    <div id='id'>ID</div>
                    <div id='email'>Email</div>
                </div>
            </div>

        </div>



        <script type="text/javascript">
            var user_u_id = '<?=$user_u_id?>';
        </script>
        <script src="main.js"></script>

        <script src="gglogin.js"></script>
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    <?php endif; ?>
    </body>
</html>
