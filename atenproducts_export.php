<?php
// Include required files
include_once("../stms/conf.php");
#echo "aaa";
function saveLog($ref_id, $newval, $oldval, $newval2, $oldval2, $by, $category, $action) {
    global $locale;
    global $spec_lang, $updateTable;
    global$conn;
    $insert = "insert into prd_log( ref_id,ref_table,category,action,oldvalue1,newvalue1,oldvalue2,newvalue2,version,createby) value('$ref_id','$updateTable','$category','$action','$oldval','$newval','$oldval2','$newval2',1,'$by');";
    #echo $insert;
    $conn->Execute($insert);
}

function getOS($product_id){
     global $conn;
     $sql = "select name from product_entities a left join os_compatibilities b on a.entity_id = b.id where a.product_id = $product_id && a.entity_type='os_compatibility' && b.domain='public' && a.domain='public' ";
     $rs_poutlet = $conn->Execute($sql); 
         while (!$rs_poutlet->EOF) {
        $name[] = $rs_poutlet->fields[name];
        $rs_poutlet->MoveNext();
    }
    return join(",",$name);
}

function getDatasheet($id,$lang_str){
    global $conn;
    $sql = "select file from product_entities a left join spec_sheets b on a.entity_id = b.id where a.product_id = $id && a.entity_type='spec_sheet' && a.locale='$lang_str' && b.domain='public' && a.domain='public' ";
    $rs_poutlet = $conn->Execute($sql); 
        while (!$rs_poutlet->EOF) {
        $file = $rs_poutlet->fields[file];
       $name[] = "http://assets.aten.com/" . $file;
       $rs_poutlet->MoveNext();
   }
   return join(",",$name);  
}

function getAwards($product_id){
     global $conn;
     $sql = "select * from product_entities a left join awards b on a.entity_id = b.id left join award_locales c on b.id=c.award_id where a.product_id = $product_id &&  a.entity_type='award' && a.domain='public' && b.domain='public' && c.domain='public' && c.locale='global/en' ";
     $rs_poutlet = $conn->Execute($sql); 
         while (!$rs_poutlet->EOF) {
        $name[] = $rs_poutlet->fields[name];
        $rs_poutlet->MoveNext();
    }
    return join(",",$name);
}

function getCertification($product_id){
     global $conn;
     $sql = "select * from product_entities a left join certifications b on a.entity_id = b.id where a.product_id = $product_id && a.entity_type='certification' && a.domain='public' && b.domain='public' && b.locale='global/en' && a.locale='global/en'";
     $rs_poutlet = $conn->Execute($sql); 
         while (!$rs_poutlet->EOF) {
        $name[] = $rs_poutlet->fields[name];
        $rs_poutlet->MoveNext();
    }
    return join(",",$name);
}

function getCompatibleproducts($id,$lang_str){
    global $conn;
    $sql = "select a.related_product_id,a.product_id,b.name as refprf,c.name from product_related_products a
    left join products b on a.related_product_id = b.id
    left join products c on a.product_id = c.id
    where (related_product_id = $id || product_id = $id ) && a.locale='$lang_str' && a.domain='public'
    && b.domain='public' && c.domain='public'";
    $rs_poutlet = $conn->Execute($sql); 
    while (!$rs_poutlet->EOF) {
        $related_product_id = $rs_poutlet->fields[related_product_id];
        $product_id = $rs_poutlet->fields[product_id];
        if($related_product_id==$id){
            $result[] = $rs_poutlet->fields[name];
        }else{
            $result[] = $rs_poutlet->fields[refprf];
        }
        $rs_poutlet->MoveNext();
    }

    return join(",",$result);
}

function getDiscontinued($enddate){
    $today_dt = new DateTime($today);
    if($enddate > $today_dt){
        return true;
    }else{
        return false;
    }
}

function getSpec($pid,$spec_lang) {

    if($spec_lang=="en"){
        $table = "prd_spec_mapping";
    }else{
        $table = "prd_spec_mapping_locales";
    }
        

    global $conn;


    $sql = "select a.value,a.mid ,a.poutlet ,a.spid,a.chid,b.sname as spidname,c.sname as chidname  from $table a left join prd_spec d  on a.spid = d.spid left join prd_spec_glossary b on d.gid = b.gid left join prd_spec_child e on a.chid = e.chid left join prd_spec_glossary c on e.gid = c.gid  where c.lang='$spec_lang' && a.lang='$spec_lang' && value not like 'remove-%' && pid='$pid' order by a.poutlet,a.order_index ";
    $sql = "select '0' as stage , a.value,a.mid ,a.poutlet ,a.spid,a.chid,g.sname as spidname,f.sname as chidname  from $table a left join prd_spec d  on a.spid = d.spid left join prd_spec_glossary b on d.gid = b.gid left join prd_spec_glossary g on b.gid = g.parentid left join prd_spec_child e on a.chid = e.chid left join prd_spec_glossary c on e.gid = c.gid left join prd_spec_glossary f on c.gid = f.parentid where g.lang='$spec_lang'  && ( f.lang='$spec_lang'  || f.lang is null ) && a.lang = '$spec_lang' && value not like 'remove-%' && pid='$pid' order by a.poutlet,a.order_index,a.mid";

    $rs_poutlet = $conn->Execute($sql);

    while (!$rs_poutlet->EOF) {
        $poutlet = $rs_poutlet->fields[poutlet];
        $poutlet_arr[] = $rs_poutlet->fields[poutlet];
        $spid = $rs_poutlet->fields[spidname];
        $chid = $rs_poutlet->fields[chidname];
        $tmpValue = $rs_poutlet->fields[value];
        if ($tmpValue != "") {
            $value[$spid][$chid][$poutlet] = $tmpValue;
        } else {
            $value[$spid][$chid][$poutlet] = "TBD";
        }
        $sid[$spid] = $rs_poutlet->fields[spid];
        $cid[$spid][$chid] = $rs_poutlet->fields[chid];
        $mid[$spid][$chid][$poutlet] = $rs_poutlet->fields[mid];
        $rs_poutlet->MoveNext();
    }

    if (sizeof($value)) {
        $sql = "select poutlet as cnt from $table where pid='$pid' group by poutlet ";
        $rs_cnt = $conn->Execute($sql);
        $cnt = $rs_cnt->recordCount();
        $cnt++;
        $del_html = "";
        $edit_html = "";
        $result = "<table class='spec_table'>";

        if ($cnt > 2) {
            $result .= "<tr><td class='function'>Function</td>";
            $poutlet_arr = array_unique($poutlet_arr);
            foreach ($poutlet_arr as $key => $poutlet) {
                $result .= "<td class='modelname'>$poutlet</td>";
            }
            $result .= "</tr>";
        }


        if (sizeof($value)) {

            foreach ($value as $spid => $svalue) {
                $sid_str = $sid[$spid];
                $showonce = false;

                foreach ($svalue as $chid => $cvalue) {
                    if (!$showonce) {
                        $showonce = true;
                        if (sizeof($svalue) == 1) {
                            //show title
                            if ($chid == "") {
                                $result .="<tr>$del_html<td class='title'>$spid</td>";
                            } else {
                                $result .="<tr>$del_html<td colspan='$cnt' class='title'>$spid</td></tr>";
                            }
                        } else {
                            $result .="<tr>$del_html<td colspan='$cnt' class='title'>$spid</td></tr>";
                        }
                    }

                    if ($chid == "") {
                        foreach ($cvalue as $poutlet => $pvalue) {

                            $pvalue = nl2br($pvalue);
                            $pvalue = trim($pvalue);
                            $mid_str = $mid[$spid][$chid][$poutlet];
                            $result .="<td class='value'>$pvalue</td>";
                        }
                    } else {
                        $cid_str = $cid[$spid][$chid];
                        $result .="<tr>$del_html<td class='subtitle'>$chid</td>";
                        foreach ($cvalue as $poutlet => $pvalue) {
                            $pvalue = nl2br($pvalue);
                            $pvalue = trim($pvalue);
                            $mid_str = $mid[$spid][$chid][$poutlet];
                            $result .="<td class='value'>$pvalue</td>";
                        }
                    }
                    $result .="$edit_html";
                }
                $result .="</tr>";
            }
        }
    }
    $result .="</table>";
    return $result;
}

$lang_str = $_POST['lang'];
$products = $_POST['prds'];
$pwd = $_POST['pwd'];
$email = $_POST['email'];


$privatekey = "6LdONjsUAAAAAL-WlG_msrUjmXw7s4KlHr9jPE5i";
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

function imgPath($path){
    if($path==""){
        return "";
    }else{
        return "http://assets.aten.com/".$path;
    }
}




if($pwd=="ATENBE" || $pwd=="ATENSD1" || $pwd=="ATENSD2" || $pwd=="ATENSD3" || $pwd=="ATENGLOBAL"){
    exportTranslate($lang_str,$products,$email,$pwd);
}else{
    echo "<br/><br/>You have entered an invalid password";
    echo "<div>Go <a href='/global/en/products/data-export/'>Back</a></div>";
    exit;
}
function exportTranslate($lang_str,$products,$email,$pwd) {
    global$conn;
    
    #$lang_str = "de/de";
    #$langforspec = "de";
    $lang = explode("/", $lang_str);
    $spec_lang = $lang[1];
    if ($spec_lang == "zh" || $spec_lang == "ja" || $spec_lang == "ko") {
        switch ($lang_str) {
            case "cn/zh":
                $langforspec = "CN";
                break;
            case "tw/zh":
                $langforspec = "TW";
                break;
            case "jp/ja":
                $langforspec = "JP";
                break;
            case "kr/ko":
                $langforspec = "KR";
                break;
        }
    }else{
        $langforspec = $spec_lang;
    }


    include_once("../stms/updateByFile/readXls/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    
    //require '../stms/updateByFile/readXls/PHPExcel.php';
// Set properties  
    $objPHPExcel->getProperties()->setCreator("ATEN")
            ->setLastModifiedBy("ATEN")
            ->setTitle("ATEN Products data")
            ->setSubject("ATEN Products data")
            ->setDescription("ATEN Products data");
            #->setKeywords("office 2007 openxml php")
            #->setCategory("Test result file");

    $rows = 2;
    #echo "aa2aa";
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(100);
    #$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(100);
    



    $sql = "select id from categories where  domain='public' && deleted=0";
    #echo $sql;
    $rs_spec = $conn->Execute($sql);
    
    while (!$rs_spec->EOF) {
        $id[] = $rs_spec->fields[id];
        $rs_spec->MoveNext();
    }

    $ids = join(",", $id);
if($products!=""){
    $products = rtrim($products, ",");
    $wstr_prd = "&& a.name in($products)";
}

// 排除下架產品
// $sql = "select a.end_date,a.eol, a.id, a.name,d.name as subcategory ,f.name as category,b.description,b.features,a.image_rear,a.image_front,a.image_others,a.image_45,b.diagram,b.overview_1,b.overview_2,b.patent_number,b.package_content from products a left join product_locales b on a.id=b.product_id left join categories c on a.subcategory_id = c.id
// left join category_locales d on c.id=d.category_id left join categories e on c.parent_id = e.id left join category_locales f on e.id=f.category_id
// where a.domain='public' && b.domain='public'  && c.domain='public' && d.domain='public' && e.domain='public' && f.domain='public'  
// && a.subcategory_id in ($ids) && b.locale='$lang_str' && d.locale='$lang_str' && (b.start_date is null || b.start_date < now())
// && (b.end_date is null || b.end_date > now()) && f.locale='$lang_str' && features is not null order by a.name";

$sql = "select b.end_date,b.eol, a.id, a.name,d.name as subcategory ,f.name as category,b.description,b.features,a.image_rear,a.image_front,a.image_others,a.image_45,b.diagram,b.overview_1,b.overview_2,b.patent_number,b.package_content from products a left join product_locales b on a.id=b.product_id left join categories c on a.subcategory_id = c.id
left join category_locales d on c.id=d.category_id left join categories e on c.parent_id = e.id left join category_locales f on e.id=f.category_id
where a.domain='public' && b.domain='public'  && c.domain='public' && d.domain='public' && e.domain='public' && f.domain='public'  
&& a.subcategory_id in ($ids) && b.locale='$lang_str' && d.locale='$lang_str' && f.locale='$lang_str' && features is not null $wstr_prd order by a.name ";
    

    
    #echo $sql;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . 1, "Model Name")
            ->setCellValue('B' . 1, "Category")
            ->setCellValue('C' . 1, "Subcategory")
            ->setCellValue('D' . 1, "Description")
            ->setCellValue('E' . 1, "Features")
            ->setCellValue('F' . 1, "Image 45")
            ->setCellValue('G' . 1, "Image Rear")
            ->setCellValue('H' . 1, "Image Front")
            ->setCellValue('I' . 1, "Image Others")
            ->setCellValue('J' . 1, "Diagram")
            ->setCellValue('K' . 1, "Package content")
            ->setCellValue('L' . 1, "Specification")
            ->setCellValue('M' . 1, "Certification")
            ->setCellValue('N' . 1, "Award")
            ->setCellValue('O' . 1, "Operation System Compatibilities")
            ->setCellValue('P' . 1, "Overview 1")
            ->setCellValue('Q' . 1, "Overview 2")
            ->setCellValue('R' . 1, "Patent Number")
            ->setCellValue('S' . 1, "Compatible products ")
            ->setCellValue('T' . 1, "EOL")
            ->setCellValue('U' . 1, "Datasheet");


    $rs_poutlet = $conn->Execute($sql);
    while (!$rs_poutlet->EOF) {
        $id = $rs_poutlet->fields[id];
        $name = $rs_poutlet->fields[name];
        $category = $rs_poutlet->fields[category];
        $subcategory = $rs_poutlet->fields[subcategory];
        $description = $rs_poutlet->fields[description];
        $features = $rs_poutlet->fields[features];
        $image_45 = imgPath($rs_poutlet->fields[image_45]);
        $image_rear = imgPath($rs_poutlet->fields[image_rear]);
        $image_front = imgPath($rs_poutlet->fields[image_front]);
        $image_others = imgPath($rs_poutlet->fields[image_others]);
        $diagram = imgPath($rs_poutlet->fields[diagram]);
        $overview_1 = $rs_poutlet->fields[overview_1];
        $overview_2 = $rs_poutlet->fields[overview_2];
        $patent_number = $rs_poutlet->fields[patent_number];
        $package_content = $rs_poutlet->fields[package_content];
        $end_date = $rs_poutlet->fields[end_date];
        $EOL = $rs_poutlet->fields[eol];
        #$Discontinued =  getDiscontinued($enddate);

        $os = getOS($id);
        $awards = getAwards($id);
        $Certification =  getCertification($id);
        $spec =  getSpec($id,$langforspec);

        $Compatibleproducts =  getCompatibleproducts($id,$lang_str);
        $Datasheet =  getDatasheet($id,$lang_str);

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $rows, $name)
                ->setCellValue('B' . $rows, $category)
                ->setCellValue('C' . $rows, $subcategory)
                ->setCellValue('D' . $rows, $description)
                ->setCellValue('E' . $rows, $features)
                ->setCellValue('F' . $rows, $image_45)
                ->setCellValue('G' . $rows, $image_rear)
                ->setCellValue('H' . $rows, $image_front)
                ->setCellValue('I' . $rows, $image_others)
                ->setCellValue('J' . $rows, $diagram)
                ->setCellValue('K' . $rows, $package_content)
                ->setCellValue('L' . $rows, $spec)
                ->setCellValue('M' . $rows, $Certification)
                ->setCellValue('N' . $rows, $awards)
                ->setCellValue('O' . $rows, $os)
                ->setCellValue('P' . $rows, $overview_1)
                ->setCellValue('Q' . $rows, $overview_2)
                ->setCellValue('R' . $rows, $patent_number)
                ->setCellValue('S' . $rows, $Compatibleproducts)
                ->setCellValue('T' . $rows, $EOL)
                ->setCellValue('U' . $rows, $Datasheet);



        $rows++;
        $rs_poutlet->MoveNext();
    }


    // Rename sheet  
    $objPHPExcel->getActiveSheet()->setTitle('ATENProduct');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $newval = $lang_str;
    $newval2 = $products;
    $newval2 = str_replace("'","\\'",$newval2);
    $oldval = $pwd;
    $by = $email;
    $oldval2 = "";
    $category = "product data";
    $action = "export";
    saveLog("0", $newval, $oldval, $newval2, $oldval2, $by, $category, $action);

    // Redirect output to a client’s web browser (Excel5) 
    $filename = "ATEN Products_".$langforspec.".xls";
    ob_end_clean();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename='.$filename);
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');
    exit;
}

?>
