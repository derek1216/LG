<?php
  require_once('recaptchalib.php');
  //$privatekey = "6LdONjsUAAAAAL-WlG_msrUjmXw7s4KlHr9jPE5i";
  $privatekey = "6LcWk0wUAAAAAOC2Sph72sEQ8pyRf97BtGmNPofW";
  $privatekey = "6LcWk0wUAAAAAOC2Sph72sEQ8pyRf97BtGmNPofW";
  if(isset($_POST['g-recaptcha-response']))
  $captcha=$_POST['g-recaptcha-response'];
  if(!$captcha){
      
  }
  $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$privatekey&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
  if($response['success'] == false){
      echo "The reCAPTCHA wasn't entered correctly. Go <a href='/global/en/products/data-export/'>Back</a> and try it again.";
      exit;
  }
  else {
    echo $captcha;

    $host = $_SERVER['HTTP_REFERER'];
    if (strpos($host, '2') !== false) {
      $dbname = "2_lgaitechcare";
      $dbuser = "2lgaitechcare";
      $dbpsw ="AiteCH0950";   
    }else{
        $dbname = "R_lgaitechcare";
        $dbuser = "Rlgaitechcare";
        $dbpsw ="LgAiTe0955";
    }
    
    
    $conn = mysql_connect('DB.Server', $dbuser, $dbpsw);

    if (!$conn) {
    　die(' 連線失敗，輸出錯誤訊息 : ' . mysql_error());
    }


    #echo $sql;
    $sql="insert into `google_reg`(`key`) VALUES('$captcha')";
    mysql_select_db($dbname,$conn);
    mysql_query($sql);
    #echo $sql;
  }
  ?>