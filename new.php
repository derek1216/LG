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
$google= $_POST["google"];




$name= inject_check($name);
$id=inject_check($id);
$email= inject_check($email);
$phonenumb= inject_check($phonenumb);
$addr1= inject_check($addr1);
$addr2= inject_check($addr2);
$addr3= inject_check($addr3);
$fb_name= inject_check($fb_name);
$fb_id= inject_check($fb_id);
$_google= inject_check($google);


function inject_check($sql_str)
{
    if(!eregi('update|drop|truncate|select|insert|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str)){
        return $sql_str;
    }else{
        return "nosql";
    }
    
}

$host = $_SERVER['HTTP_REFERER'];
if (strpos($host, 'aitechcare.ptt.com') !== false) {
    if (strpos($host, '2') !== false) {
        $dbname = "2_lgaitechcare";
        $dbuser = "2lgaitechcare";
        $dbpsw ="AiteCH0950";   
    }else{
        $dbname = "R_lgaitechcare";
        $dbuser = "Rlgaitechcare";
        $dbpsw ="LgAiTe0955";
    }
    $dbname = "R_lgaitechcare";
    $dbuser = "Rlgaitechcare";
    $dbpsw ="LgAiTe0955";
    
    $conn = mysql_connect('DB.Server', $dbuser, $dbpsw);

    if (!$conn) {
    　die(' 連線失敗，輸出錯誤訊息 : ' . mysql_error());
    }

    mysql_select_db($dbname,$conn);
    if($type=="new"){
        $sql = "select * from `google_reg` where `used` = 0 && `key` = '$_google'";
        $result = mysql_query($sql);

        if(mysql_num_rows($result) > 0){
            $sql="INSERT INTO `lg_reg`(`name`, `email`, `phone`, `city`, `district`, `address`)
            VALUES ('$name','$email','$phonenumb','$addr1','$addr2','$addr3')";
             mysql_query($sql);
            $sql="update `google_reg` set `used`=1,`updatedate`=now() where `key` = '$google'";
            mysql_query($sql);
        }
        $sql="SELECT max(id) as maxid FROM `lg_reg`";
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
            $maxid = $row['maxid'];
        }

        $id = $maxid;
    }

    if($type=="fb" && $id!=""){
        $sql="update `lg_reg` set `fb_id`='$fb_id',`fb_name`='$fb_name',`fb_update_date`=now() where `id`=$id && `fb_name` is null && `fb_id` is null";
        mysql_query($sql);
    }

    #echo $sql;

    
   



    echo $id;
}else{
    echo "errorhost - ".$host;
}
?>