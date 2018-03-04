<?php

 
$conn = mysql_connect('DB.Server', '2lgaitechcare', 'AiteCH0950');
if (!$conn) {
　die(' 連線失敗，輸出錯誤訊息 : ' . mysql_error());
}
echo ' 連線成功 ';
mysql_select_db("2_lgaitechcare",$conn);
 
// This could be supplied by a user, for example
$firstname = 'fred';
$lastname  = 'fox';

// Formulate Query
// This is the best way to perform an SQL query
// For more examples, see mysql_real_escape_string()
$query = "select * from .testtable";
$insert = "insert into testtable(aaa,bbb) value(2,'okok')";
mysql_query($insert);
// Perform Query
$result = mysql_query($query);

// Check result
// This shows the actual query sent to MySQL, and the error. Useful for debugging.
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

// Use result
// Attempting to print $result won't allow access to information in the resource
// One of the mysql result functions must be used
// See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
while ($row = mysql_fetch_assoc($result)) {
    echo $row['aaa'];
    echo $row['bbb'];
}

// Free the resources associated with the result set
// This is done automatically at the end of the script
mysql_free_result($result);

?>
