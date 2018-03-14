<?php
  require_once('recaptchalib.php');
  //$privatekey = "6LdONjsUAAAAAL-WlG_msrUjmXw7s4KlHr9jPE5i";
  $privatekey = "6LcWk0wUAAAAAOC2Sph72sEQ8pyRf97BtGmNPofW";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["g-recaptcha_response"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
         "(reCAPTCHA said: " . $resp->error . ")");
  } else {
    // Your code here to handle a successful verification
  }
  ?>