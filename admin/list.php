<?php 
$user = $_COOKIE["spec_user"];
if($user != "lgadmin"){
    header("Location: /admin/index.php");
    exit;
}

$conn = mysql_connect('DB.Server', '2lgaitechcare', 'AiteCH0950');
if (!$conn) {
　die(' 連線失敗，輸出錯誤訊息 : ' . mysql_error());
}
mysql_select_db("2_lgaitechcare",$conn);
$query = "select * from lg_reg";
$result = mysql_query($query);

$query = "select * from lg_reg where fb_id !=''";
$resultfb = mysql_query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
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
    <div>
        <p>總註冊人數 : <?php echo mysql_num_rows($result);?></p>
    </div> 
    <div>
        <p>總註冊人數(包含完成fb分享) : <?php echo mysql_num_rows($resultfb);?></p>
    </div> 
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
            echo "<td>".$row['phone']."</td>";
            echo "<td>".$row['city']."</td>";
            echo "<td>".$row['district']."</td>";
            echo "<td>".$row['address']."</td>";
            echo "<td>".$row['create_date']."</td>";
            echo "<td>".$row['fb_id']."</td>";
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


