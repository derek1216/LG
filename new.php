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

if($host =="https://2lg_aitechcare.ptt.com.tw/" || $host =="https://lg_aitechcare.ptt.com.tw/"){
    if($host !="https://2lg_aitechcare.ptt.com.tw/"){
        $dbname = "R_lgaitechcare";
        $dbuser = "Rlgaitechcare";
        $dbpsw ="LgAiTe0955";
    }else{
        $dbname = "2_lgaitechcare";
        $dbuser = "2lgaitechcare";
        $dbpsw ="AiteCH0950";
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
    echo "errorhost";
}
?>