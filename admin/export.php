<?php 
header('Content-type:application/vnd.ms-excel');  //宣告網頁格式
header('Content-Disposition: attachment; filename=export.xls');  //設定檔案名稱



?>
<?php 
$user = $_COOKIE["spec_user"];
if($user != "lgadmin"){
    header("Location: /admin/index.php");
    exit;
}

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

#$conn = mysql_connect('DB.Server', '2lgaitechcare', 'AiteCH0950');
$conn = mysql_connect('DB.Server', $dbuser, $dbpsw);

if (!$conn) {
　die(' 連線失敗，輸出錯誤訊息 : ' . mysql_error());
}


mysql_select_db($dbname,$conn);

$date = $_GET['date'];
$distinct = $_GET['distinct'];

if($date!=""){
    $wstr = "and `create_date` BETWEEN '".$date." 00:00:00' AND '".$date." 23:59:59' ";
}

$hacker = " and `name` not in ('彭如鈺' ,'林秀玉', '彭淑玲', '陳獻文') ";

if($distinct!=""){
    $distinct = " group by name";
}

$query = "select * from (select * from lg_reg union select * from lg_reg0314 order by create_date desc) as n where 1=1 $wstr $hacker $distinct order by create_date desc";
$result = mysql_query($query);




?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>LG無人家政公司 - 後台</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <style>
      .table thead th{text-align: center;vertical-align: middle;}
  </style>
</head>
<body style="padding:10px;background-image: url('../image/index/bg.png');">

<div class="">          
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>編號</th>
        <th>姓名</th>
        <th>信箱</th>
        <th>手機</th>
        <th>縣市</th>
        <th>區域</th>
        <th>住址</th>
        <th>新增時間</th>
        <th>臉書ID</th>
        <th>臉書姓名</th>
        <th>臉書更新時間</th>
      </tr>
    </thead>
    <tbody>
        <?php
        
        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['name']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "<td>> ".$row['phone']."</td>";
            echo "<td>".$row['city']."</td>";
            echo "<td>".$row['district']."</td>";
            echo "<td>".$row['address']."</td>";
            echo "<td>".$row['create_date']."</td>";
            echo "<td>> ".$row['fb_id']."</td>";
            echo "<td>".$row['fb_name']."</td>";
            echo "<td>".$row['fb_update_date']."</td>";
            echo "</tr>";
        }
        
        ?>
    </tbody>
  </table>
</div>

</body>
</html>



