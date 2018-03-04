<?php
/*
*  @description : 常用的function

* 跳轉URL
* @param :
    $url 頁面名稱
    $msg 訊息
*/
function goURL($url='', $msg='')
{
    $url = ($url)?$url:$_SERVER['HTTP_REFERER'];
    $url = ($url)?$url:'/';
    $JSstr = '';
    if($msg)
    {
        $JSstr .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $JSstr .= "<script type=\"text/javascript\">alert('".$msg."');</script>\n";
    }
    $JSstr .= "<script type=\"text/javascript\">location.href='".$url."'</script>\n";
    $JSstr .= "<meta http-equiv=\"refresh\" content=\"1; url=".$url."\">";
    echo $JSstr;
    exit();
}

/* 發送信件
* @param :
    $to_mail    收件者位置
    $form_mail  發信者位置
    $mail_topic 信件主旨
    $content    信件內容
*/
function sendEmail($from_name,$from_mail,$to_mail,$mail_subject,$sub_body)
{
    $headers = 'Content-type: text/html; charset=utf-8' . "\r\n";  
    $headers .= "From: ". $from_name ."<". $from_mail. ">" . "\r\n"; // 請自行替換寄件地址	
    $charset = "utf-8";
    mail($to_mail, "=?UTF-8?B?".base64_encode($mail_subject)."?=", $sub_body,  $headers);
}

/* 切割字元
@param :
    $string  為原字串
    $length  為截取長度
*/
function cutStr($string, $length, $more)
{
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);   
    for($i=0; $i<count($info[0]); $i++) 
    {
        $wordscut .= $info[0][$i];
        $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
        if ($j > $length - 3 && $more) 
        {
            return $wordscut."...";
        }
    }
    return join('', $info[0]);;
}

/*  
語系
*/
function checkLang($lang)
{
    session_register('login_language') ;
    session_register('se_now_lang') ;
    $ip      = $_SERVER['REMOTE_ADDR'];
    $user_ip = explode(".",$ip);
    
    if($lang != null)
    {
        if( strlen($lang) > 2 || ($lang != $now_lang))
        {
            $_SESSION['se_now_lang'] = $lang ;
            header("LOCATION: .") ;
            exit ;
        }
    }
    else
    {
        /*-------大陸----------*/
        $chinacontents = file ('china.txt');
        while(list($line_num,$line)=each($chinacontents)) 
        {
            $data=explode(" ",htmlspecialchars($line));
            $ip_country=$data[1];
            $ip_zone=$data[2];
            /*Class B*/
            if($ip_zone>=65536)
            {
                $b=$ip_zone/65536;
                $ip_countryz=explode(".",$ip_country);
                for($i=0;$i<$b;$i++)
                {
                    if(($user_ip[0]==$ip_countryz[0])&&($user_ip[1]==$ip_countryz[1]))
                    {
                        $_SESSION['login_language'] = "cn" ;
                    }
                    $ip_countryz[1]++;
                }
            }
            /*Class C*/
            if($ip_zone>=256 && $ip_zone<65536)
            {
                $c=$ip_zone/256;
                $ip_countryz=explode(".",$ip_country);
                for($i=0;$i<$c;$i++)
                {
                    if(($user_ip[0]==$ip_countryz[0])&&($user_ip[1]==$ip_countryz[1])&&($user_ip[2]==$ip_countryz[2]))
                    {
                        $_SESSION['login_language'] = "cn" ;
                    }
                    $ip_countryz[2]++;
                }
            }
        }
        /*-------臺灣----------*/
        $fcontents = file ('taiwan.txt');
        while(list($line_num,$line)=each($fcontents))
        {
            $data=explode(" ",htmlspecialchars($line));
            $ip_country=$data[1];
            $ip_zone=$data[2];
            /*Class B*/
            if($ip_zone>=65536)
            {
                $b=$ip_zone/65536;
                $ip_countryz=explode(".",$ip_country);
                for($i=0;$i<$b;$i++)
                {
                    if(($user_ip[0]==$ip_countryz[0])&&($user_ip[1]==$ip_countryz[1]))
                    {
                        $_SESSION['login_language'] = "ch" ;
                    }
                    $ip_countryz[1]++;
                }
            }
            /*Class C*/
            if($ip_zone>=256 && $ip_zone<65536)
            {
                $c=$ip_zone/256;
                $ip_countryz=explode(".",$ip_country);
                for($i=0;$i<$c;$i++)
                {
                    if(($user_ip[0]==$ip_countryz[0])&&($user_ip[1]==$ip_countryz[1])&&($user_ip[2]==$ip_countryz[2]))
                    {
                        $_SESSION['login_language'] = "ch" ;

                    }
                    $ip_countryz[2]++;
                }
            }
        }
        if($_SESSION['se_now_lang'])
        {
            $now_lang = $_SESSION['se_now_lang'] ;

        }
        else
        {
            $now_lang = $_SESSION['login_language'] ;
        }
    }
    return $now_lang ;
}

function uniDecode($str,$charcode)
{
    $text = preg_replace_callback("/%u[0-9A-Za-z]{4}/",toUtf8,$str);
    return mb_convert_encoding($text, $charcode, 'utf-8');
}

function toUtf8($ar)
{
    foreach($ar as $val)
    {
        $val = intval(substr($val,2),16);
        if($val < 0x7F)
        {// 0000-007F
            $c .= chr($val);
        }
        elseif($val < 0x800) 
        {// 0080-0800
            $c .= chr(0xC0 | ($val / 64));
            $c .= chr(0x80 | ($val % 64));
        }
        else
        {// 0800-FFFF
            $c .= chr(0xE0 | (($val / 64) / 64));
            $c .= chr(0x80 | (($val / 64) % 64));
            $c .= chr(0x80 | ($val % 64));
        }
    }
    return $c;
}

/*
 * 取得登入者的menu功能
 * */
function getUserMenu($db,$key_id){
    $sql_q = "Select m.p_id,m.mp_id,p.name,p.type,p.url,ifnull(f.func,'') as func from sys_user_menu m left join sys_program p on m.p_id = p.id left join sys_user_func f on m.u_key_id = f.u_key_id and m.p_id = f.p_id where m.u_key_id = ". transString($key_id,"0",false) ." and m.mp_id <> '' and p.del = 0 order by m.mp_sort,m.p_sort";
    //$result = $db -> query($sql_q);
    $menu_name_ist = '';
    $menu_module_ist = '';
    $menu_url_list = '';
    $menu_func_list = '';
    if ($result = $db -> query($sql_q)){
        while( $record = $db -> getArray($result)){
            if ("" == $menu_name_ist){
                $menu_name_ist .= $record['p_id'] ."|". $record['name'];
                $menu_module_ist .= $record['p_id'] ."|". $record['mp_id'];
                $menu_url_list .= $record['p_id'] ."|". $record['url'];
                $menu_func_list .= $record['p_id'] ."|". $record['func'];
            }
            else{
                $menu_name_ist .= "|". $record['p_id'] ."|". $record['name'];
                $menu_module_ist .= "|". $record['p_id'] ."|". $record['mp_id'];
                $menu_url_list .= "|". $record['p_id'] ."|". $record['url'];
                $menu_func_list .= "|". $record['p_id'] ."|". $record['func'];
            }
        }
    }
    return array($menu_name_ist,$menu_module_ist,$menu_url_list,$menu_func_list);
}

/*
 * YYYYMMDDHHNNSS轉成其他日期時間顯示格式
 * 0: YYYY/MM/DD HH:NN:SS
 * 1:YYYY-MM-DD HH:NN:SS
 * 2:YYYY/MM/DD
 * 3:YYYY-MM-DD
 * 4.YY/MM/DD (民國年)
 * 5.YY-MM-DD (民國年)
 * */
function transDateTimeString($date_time_string,$format){
    if (empty($date_time_string)) return "";
    switch($format){
        case 0:
            $trans_string = substr($date_time_string,0,4) ."/". substr($date_time_string,4,2) ."/". substr($date_time_string,6,2) ." ". substr($date_time_string,8,2) .":". substr($date_time_string,10,2) .":". substr($date_time_string,12,2);
            break;
        case 1:
            $trans_string = substr($date_time_string,0,4) ."-". substr($date_time_string,4,2) ."-". substr($date_time_string,6,2) ." ". substr($date_time_string,8,2) .":". substr($date_time_string,10,2) .":". substr($date_time_string,12,2);
            break;
        case 2:
            $trans_string = substr($date_time_string,0,4) ."/". substr($date_time_string,4,2) ."/". substr($date_time_string,6,2);
            break;
        case 3:
            $trans_string = substr($date_time_string,0,4) ."-". substr($date_time_string,4,2) ."-". substr($date_time_string,6,2);
            break;
        case 4:
            $trans_string = (string)((int)substr($date_time_string,0,4) - 1911) ."/". substr($date_time_string,4,2) ."/". substr($date_time_string,6,2);
            break;
        case 5:
            $trans_string = (string)((int)substr($date_time_string,0,4) - 1911) ."-". substr($date_time_string,4,2) ."-". substr($date_time_string,6,2);
            break;
    }
    return $trans_string;
}

/*
 * 各種格式的驗證
 * */
/*
 * 各種格式的驗證
 * */
function isValidFormat($str,$formatType,$reg_pattern){
    switch ($formatType){
        case "userid": //(a-z, A-Z, 0-9) & 5-20 characters
            return preg_match('/^[a-z\d_]{5,20}$/i',$str);
            break;
        case "email":
            //return preg_match("/^[a-zA-Z0-9\-\_][\w\.-]*[a-zA-Z0-9\-\_]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/", $str);
            //return preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$str);
            return preg_match('/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,6})$/',$str);
            break;
        case "url":
            //return preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$str);
            return preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$str);
            break;
        case "tel":
            //return preg_match('/\(?\d{3}\)?[-\s.]?\d{3}[-\s.]\d{4}/x',$str);
            //return preg_match('/^([0-9]|[\-])+$/',$str);
            return preg_match('/^(([0-9]|[\-\#\ ]){7,20})+$/',$str);
            break;
        case "mobile":
            return preg_match('/^(09)[0-9]{8}$/',$str);
            break;
        case "ip":
            return preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',$str);
            break;
        case "creditcard":
            if (16 != strlen($str)){
                return 0;
            }else{
                return preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/',$str);
            }
            break;
        case "domain":
            return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',$str);
            break;
        case "invoice":
            if (10 != strlen($str)){
                return 0;
            }else{
                return (preg_match('/^[A-Z]+$/',substr($str,0,2)) && preg_match('/^[0-9]+$/',substr($str,2)));
            }
            break;
        case "idno":
            return checkIdNo($str);
            break;
        case "fbid":
            return preg_match('/^[0-9]+$/',$str);
            break;
        case "other":
            return preg_match($reg_pattern,$str);
            break;
    }
}

/*
 * 判斷是否使用異動功能(如 新增 A/ 修改M / 刪除D / 其它O)
 * */
function checkUserFunc($func_list,$pg_id,$func_id){
    $can_used = false;
    if (empty($func_list)){
        return $can_used;
        exit;
    }
    $arr_func_list = explode("|",$func_list);
    for ($m = 0;$m < count($arr_func_list) / 2;$m++){
        if ($pg_id == $arr_func_list[($m * 2)]){
            $func = $arr_func_list[($m * 2) + 1];
            $pos = strpos($func,$func_id);
            if (gettype($pos)=="integer") $can_used = true;
        }
    }
    return $can_used;
}

/*
 * 取出目前所在的功能名稱
 * */
function getMenuName($name_list,$pg_id){
    $menu_name = "";
    if (empty($name_list)){
        return $menu_name;
        exit;
    }
    $arr_name_list = explode("|",$name_list);
    for ($m = 0;$m < count($arr_name_list) / 2;$m++){
        if ($pg_id == $arr_name_list[($m * 2)]){
            $menu_name = $arr_name_list[($m * 2) + 1];
        }
    }
    return $menu_name;
}

/*
 * 取代指定特殊字元
 * 0 : for SQL
 * 1 : for text field or normal show (excpet textarea field)
 * 2 : for textarea field show
 * 3 : for textarea field input
 * 4 : for javascript function input parameter
 * 5 : for javascript parameter
 * 6 : for fckeditor input value
 * */
function transString($str,$replace_type,$parse_empty){
    $str = trim($str);
    if (!get_magic_quotes_gpc()){
        switch($replace_type){
            case "0":
                $str = str_replace("'","''",$str);
                //$str = preg_replace("/--+/","-",$str);
                break;
            case "1":
                $str = str_replace("'","&apos;",$str);
                $str = str_replace("\"","&quot;",$str);
                $str = str_replace(">","&gt;",$str);
                $str = str_replace("<","&lt;",$str);
                break;
            case "2":
                $str = str_replace("'","&apos;",$str);
                $str = str_replace("\"","&quot;",$str);
                $str = str_replace(">","&gt;",$str);
                $str = str_replace("<","&lt;",$str);
                $str = str_replace(array("\r\n","\r","\n"),"<br />",$str);
                break;
            case "3":
                $str = str_replace(">","&gt;",$str);
                $str = str_replace("<","&lt;",$str);
                break;
            case "4":
                $str = str_replace("'","\'",$str);
                $str = str_replace('"','\"',$str);
                break;
            case "5":
                $str = str_replace("'","\'",$str);
                $str = str_replace('"','\"',$str);
                break;
            case "6":
                break;
        }
    }else{
        switch($replace_type){
            case "0":
                break;
            case "1":
                $str = htmlspecialchars(stripslashes($str) ,ENT_QUOTES);
                break;
            case "2":
                $str = htmlspecialchars(stripslashes($str) ,ENT_QUOTES);
                $str = str_replace(array("\r\n","\r","\n"),"<br />",$str);
                break;
            case "3":
                $str = htmlspecialchars(stripslashes($str) ,ENT_QUOTES);
                break;
            case "4":
                $str = htmlspecialchars(stripslashes($str) ,ENT_QUOTES);
                break;
            case "5":
                $str = str_replace(array("\r\n","\r","\n"),"\\r\\n",$str);
                break;
            case "6":
                $str = stripslashes($str);
                break;
        }
    }
    if ($parse_empty) parseEmpty($str);
    return $str;
}

function parseEmpty($parse_string){
    if (empty($parse_string))
        return "&nbsp;";
    else
        return $parse_string;
}

function getListTag($list,$tag_type,$tag_id,$tag_value,$tag_func,$tag_class,$ext_list,$cols,$separate_count){
    $list_tag = "";
    if (empty($list)){
        return $list_tag;
        exit;
    }
    if (empty($tag_value)) $tag_value = "";
    if (empty($cols)) $cols = 1;
    if (empty($separate_count)) $separate_count = 2;
    if (!empty($ext_list)) $list .= $ext_list ."|". $list;
    $arr_list = explode("|",$list);
    $arr_tag_value = explode(",",$tag_value);
    switch ($tag_type){
        case "SELECT":
            if (!empty($tag_func)) $func = "onchange=\"". $tag_func ."\"";
            if (!empty($tag_class)) $class = "class=\"". $tag_class ."\"";
            if ($cols > 1)
                @$list_tag = "<select name=\"". $tag_id ."[]\" id=\"". $tag_id ."[]\" ". $func ." ". $class ." multiple=\"multiple\">\n";
            else
                @$list_tag = "<select name=\"". $tag_id ."\" id=\"". $tag_id ."\" ". $func ." ". $class .">\n";
            for ($i = 0; $i < count($arr_list) / $separate_count; $i++){
                $is_select = "";
                for ($j = 0; $j < count($arr_tag_value); $j++){
                    if (@$arr_list[($i * $separate_count)] == $arr_tag_value[$j]) $is_select = "selected";
                }
                $list_tag .= "<option value=\"". transString($arr_list[($i * $separate_count)],"1",false) ."\" ". $is_select .">". transString($arr_list[($i * $separate_count) + 1],"1",false) ."</option>\n";
            }
            $list_tag .="</select>\n";
            break;
        case "RADIO":
            if (!empty($tag_func)) $func = "onclick=\"". $tag_func ."\"";
            if (!empty($tag_class)) $class = "class=\"". $tag_class ."\"";
            $list_tag = "<table>\n";
            $k = 1;
            for ($i = 0; $i < count($arr_list) / $separate_count; $i++){
                $is_checked = "";
                if (0 == ((($i * $separate_count) / $separate_count) % $cols)){
                    $list_tag .= "<tr>\n";
                    if (($i * $separate_count) > 0){
                        $list_tag .= "</tr>\n<tr>\n";
                        $k = 1;
                    }
                    $k++;
                }
                for ($j = 0; $j < count($arr_tag_value); $j++){
                    if ($arr_list[($i * $separate_count)] == $arr_tag_value[$j]) $is_checked = "checked";
                }
                $list_tag .= "<td ". $class ."><input type=\"radio\" name=\"". $tag_id ."\" value=\"". transString($arr_list[($i * $separate_count)],"1",false) ."\" ". $is_checked .">". transString($arr_list[($i * $separate_count) + 1],"1",false) ."</td>\n";
            }
            for ($l = $k; $l <= $cols; $l++){
                $list_tag .= "<td>&nbsp;</td>\n";
                if (0 == ($l % $cols)) $list_tag .= "</tr>\n";

            }
            $list_tag .= "</table>\n";
            break;
        case "CHECKBOX":
            if (!empty($tag_func)) $func = "onclick=\"". $tag_func ."\"";
            if (!empty($tag_class)) $class = "class=\"". $tag_class ."\"";
            $list_tag = "<table>\n";
            $k = 1;
            for ($i = 0; $i < count($arr_list) / $separate_count; $i++){
                $is_checked = "";
                if (0 == ((($i * $separate_count) / $separate_count) % $cols)){
                    $list_tag .= "<tr>\n";
                    if (($i * $separate_count) > 0){
                        $list_tag .= "</tr>\n<tr>\n";
                        $k = 1;
                    }
                    $k++;
                }
                for ($j = 0; $j < count($arr_tag_value); $j++){
                    if ($arr_list[($i * $separate_count)] == $arr_tag_value[$j]) $is_checked = "checked";
                }
                if ((count($arr_list) / $separate_count) > 1)
                    $list_tag .= "<td ". $class ."><input type=\"checkbox\" name=\"". $tag_id ."[]\" value=\"". transString($arr_list[($i * $separate_count)],"1",false) ."\" ". $is_checked .">". transString($arr_list[($i * $separate_count) + 1],"1",false) ."</td>\n";
                else
                    $list_tag .= "<td ". $class ."><input type=\"checkbox\" name=\"". $tag_id ."\" value=\"". transString($arr_list[($i * $separate_count)],"1",false) ."\" ". $is_checked .">". transString($arr_list[($i * $separate_count) + 1],"1",false) ."</td>\n";
            }
            for ($l = $k; $l <= $cols; $l++){
                $list_tag .= "<td>&nbsp;</td>\n";
                if (0 == ($l % $cols)) $list_tag .= "</tr>\n";

            }
            $list_tag .= "</table>\n";
            break;
        case "":
            for ($i = 0; $i < count($arr_list); $i++){
                for ($j = 0; $j < count($arr_tag_value); $j++){
                    if (@$arr_list[($i * $separate_count)] == $arr_tag_value[$j]){
                        if ("" == $list_tag){
                            $list_tag .= transString($arr_list[($i * $separate_count) + 1],"1",false);
                        }else{
                            $list_tag .= ",". transString($arr_list[($i * $separate_count) + 1],"1",false);
                        }
                    }
                }
            }
            break;
    }
    return $list_tag;
}

function getCodeList($db,$type,$where_sql,$order_sql,$extra_code){
    $sql_q = "Select id,content from sys_code where type = '". $type ."' and del = 0";
    if (!empty($where_sql)) $sql_q .= $where_sql;
    if (!empty($order_sql))
        $sql_q .= " order by ". $order_sql;
    else
        $sql_q .= " order by id";
    $code_list = "";
    if (!empty($extra_code)) $code_list .= $extra_code;
    if ($code = $db -> query($sql_q)){
        while($record = $db -> getArray($code)){
            if ("" == $code_list)
                $code_list .= $record['id'] ."|". $record['content'];
            else
                $code_list .= "|". $record['id'] ."|". $record['content'];
        }
    }
    return $code_list;
}

/*
身份證檢查
*/
function checkIdNo($chk_idNo){
    $ID_ABC_Data = "A10B11C12D13E14F15G16H17I34J18K19L20M21N22O35P23Q24R25S26T27U28V29W32X30Y31Z33"; 
    if(strlen($chk_idNo) != 10){ 
        return false;
        exit; 
    } 

    $InputID = strtoupper($chk_idNo);//改成大寫例如$chk_idNo=a123456789 
    $id_first_wd = substr($InputID,0,1);//取出身份證的第一字母 
    $id_last_wd = substr($InputID,1);//取出身份證的英文字母後面的數字 
    $idno = strrpos($ID_ABC_Data,$id_first_wd) + 1;//取出$ID_ABC_Data字母後的兩個位置 
    $id_abc_wd = substr($ID_ABC_Data,$idno,2);//取出$ID_ABC_Data字母後的兩個數字 
    $InputID = $id_abc_wd . $id_last_wd;//合成新數目;12123456789 
    $GetNo = 1; 
    $SUM = substr($InputID,0,1); //$SUM=2 

    for($i = 9;$i > 0;$i--){ 
        $SUM += substr($InputID,$GetNo,1) * $i; 
        $GetNo++; 
    } 

    if (substr($InputID,-1,1) != substr((10 - substr($SUM,-1,1)),-1,1)){ 
        return false;
    }else{ 
        return true;
    }
}

function base64_to_png($inputFile,$outputFile) {
    $imageData = $inputFile;   
    
    $file = fopen( $outputFile, "w" );
    if (!fwrite($file, base64_decode($imageData))){
        return false;
        exit;
    }
    fclose($file);
    return true;
}

//檢查日期
function validate_Date($mydate,$format = 'DD-MM-YYYY') {
    if ($format == 'YYYY-MM-DD') list($year, $month, $day) = explode('-', $mydate);
    if ($format == 'YYYY/MM/DD') list($year, $month, $day) = explode('/', $mydate);
    if ($format == 'YYYY.MM.DD') list($year, $month, $day) = explode('.', $mydate);

    if ($format == 'DD-MM-YYYY') list($day, $month, $year) = explode('-', $mydate);
    if ($format == 'DD/MM/YYYY') list($day, $month, $year) = explode('/', $mydate);
    if ($format == 'DD.MM.YYYY') list($day, $month, $year) = explode('.', $mydate);

    if ($format == 'MM-DD-YYYY') list($month, $day, $year) = explode('-', $mydate);
    if ($format == 'MM/DD/YYYY') list($month, $day, $year) = explode('/', $mydate);
    if ($format == 'MM.DD.YYYY') list($month, $day, $year) = explode('.', $mydate);

    if (is_numeric($year) && is_numeric($month) && is_numeric($day))
        return checkdate($month,$day,$year);

    return false;
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

function mask_data($str_data,$mask_type){
    $str_mask_data = trim($str_data);
    if ($mask_type == 'name'){
        if (mb_strlen($str_data) == 1)
            $str_mask_data = 'X';
        else if (mb_strlen($str_data) == 2)
            $str_mask_data = mb_substr($str_data,0,1,'utf-8') .'X';
        else
            $str_mask_data = mb_substr($str_data,0,mb_strlen($str_data) - 2,'utf-8') . 'X' . mb_substr($str_data,-1,1,'utf-8');
    }else if ($mask_type == 'mobile'){
        $str_mask_data = mb_substr($str_data,0,4,'utf-8') .'XXXXXX';
    }else if ($mask_type == 'email'){
        $email = explode('@',$str_data);
        //$str_mask_data = mb_substr($str_data,0,1,'utf-8') . mb_str_repeat('X',mb_strlen($email[0]) - 1) .'@'. $email[1];
        $str_mask_data = mb_substr($str_data,0,1,'utf-8') . str_repeat('X',mb_strlen($email[0]) - 1) .'@'. $email[1];
    }

    return $str_mask_data;
}

//產生CSRF Token
function get_csrf_token(){
	$csrf_token = base64_encode(openssl_random_pseudo_bytes(32));
	$_SESSION['csrf_token'] = $csrf_token;
	return $csrf_token;
}

/**
* _parse_special_char 解析html文字 轉換和過濾不必要的字元
*@param   string content     html文字
*@return  string new_content 解析後的文字
*/
function _parse_special_char($content) {

	$new_content = str_replace('&amp;', '&', $content);
	$search = array('&quot;', '&nbsp;', 'nbsp;', '&lt;', '&gt;', 'hellip;', '&amp;', ',');
	$replace = array('\"', ' ', ' ', '<', '>', '...', '', '，');
	$new_content = str_replace($search, $replace, $new_content);
	$new_content = preg_replace('/\s\s+/', ' ', $new_content);
	$new_content = preg_replace('/<.+?>/', ' ', $new_content);
	$new_content = preg_replace('/^[a-zA-Z0-9]*$/', ' ', $new_content);

	return $new_content;

}

/*
除了繁簡中文外,其他都算一個字元
*/
function get_string_length($string) {
	$len = mb_strlen($string, 'UTF-8');
	$len_r = 0;
	for ($i = 0; $i < $len; $i++) {
		$s = mb_substr($string, $i, 1, 'UTF-8');
		if (strlen($s) == 1 || strlen($s) == 2) {
			$len_r++;
		} else {
			$len_r += 2;
		}
	}

	return $len_r;
}
?>