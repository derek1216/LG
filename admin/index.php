<?php

$username = $_POST['username'];
$password = $_POST['password'];
$user = $_COOKIE["spec_user"];
if($user != "" || ($username!="" && $password!="")){
  if($username=="lgadmin" && $password=='lgadmin'){
    setcookie("spec_user",htmlspecialchars($username), time() + 18000);
    header("Location: /admin/list.php");
    exit;
  }else if($user != ""){
    header("Location: /admin/list.php");
    exit;
  }else{
    $exist=-1;
  }
}

setcookie("spec_user",'', time() + 18000);

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <head>
    <title>LG無人家政公司 - 後台</title>
    <link rel="stylesheet" href="admin.css">
    <style>
      table,
      tr,
      td {
        border: 1px solid #dedede;
        background-color: #f1f1f1;
      }
    </style>
  </head>

  <body class="module_admin locale_en login">


    <div class="wrapper">
      <div class="login-body">
        <div id="pagetitle">LG無人家政公司 - 後台
        </div>
        <div id="login_form_title" style="padding: 50px; text-align: center; background-color: red; color:#fff; font-size: 32px;display:none;">UPGEADING IN PROGRESS</div>
        <div style="display:block;">
          <h2 id="login_form_title" style="padding-top: 10px;">Login</h2>

          <form novalidate="novalidate" action="index.php" method="POST" class="form-validate" id="login_form" autocomplete="off">
            <ul class="errorStack errors">
              <?php                             if ($exist == -1) {
    echo '<li class="error"><span>Invalid username or password. Please re-enter.</span></li>';
}
?>
            </ul>

            <div class="control-group">
              <div class="email controls">
                <div class="input-field text-field">
                  <div>
                    <input placeholder="Username" name="username" id="username" value="" maxlength="255" data-rule-required="true" type="text">
                  </div>

                </div>

              </div>
            </div>

            <div class="control-group">
              <div class="pw controls">
                <div class="input-field text-field">
                  <div>
                    <input placeholder="Password" name="password" id="password" value="" maxlength="255" data-rule-required="true" type="password">
                  </div>

                </div>

              </div>
            </div>

            <div class="submit">
              <input value="Login" class="btn btn-primary" type="submit">
            </div>
          </form>
        </div>
        <div style="margin:15px 30px 0; color: #666;">For the best experience using this platform, please use Google Chrome 36.0 or above</div>
      </div>
    </div>
    <div class="power">Copyright © 2009-2018 LG Electronics. All Rights Reserved</div>
    <script>
      var isChrome = !!window.chrome && !!window.chrome.webstore;
      var isFirefox = typeof InstallTrigger !== 'undefined';
      if ((isChrome || isFirefox) == false) {
        alert("For the best experience using this platform, please use Google Chrome 36.0 or above");
      }
    </script>
  </body>

  </html>