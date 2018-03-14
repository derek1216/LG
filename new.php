<?php 
$type= $_POST["type"];
$name= $_POST["name"];
$id= $_POST["id"];
$email= $_POST["email"];
$phonenumb= $_POST["phonenumb"];
$addr1= $_POST["addr1"];
$addr2= $_POST["addr2"];
$addr3= $_POST["addr3"];
$fb_name= $_POST["fb_name"];
$fb_id= $_POST["fb_id"];

$host = $_SERVER['HTTP_REFERER'];
if (strpos($host, 'aitechcare.ptt.com') !== false) {
    //https://www.google.com/recaptcha/admin#site/340562710?setup
    //$privatekey = "6LdONjsUAAAAAL-WlG_msrUjmXw7s4KlHr9jPE5i";
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
    
    }











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
    if($type=="new"){
        $sql="INSERT INTO `lg_reg`(`name`, `email`, `phone`, `city`, `district`, `address`)
        VALUES ('$name','$email','$phonenumb','$addr1','$addr2','$addr3')";
    }

    if($type=="update" && $id!=""){
        $sql="update `lg_reg` set fb_id='$fb_id',fb_name='$fb_name',fb_update_date=now() where id=$id";
    }

    #echo $sql;

    mysql_select_db($dbname,$conn);
    mysql_query($sql);

    $sql="SELECT max(id) as maxid FROM `lg_reg`";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_assoc($result)) {
        $maxid = $row['maxid'];
    }

    echo $maxid;
}else{
    echo "errorhost - ".$host;
}
?>