<?php  
date_default_timezone_set('PRC'); //设置中国时区 
// header("Content-type: text/html; charset=utf-8"); 
header("Content-Type: application/force-download");  
header("Content-type:text/csv;charset=utf-8");  
header("Content-Disposition:filename=".date("YmdHis").".csv"); 
// exit;
//http://php.net/manual/zh/pdo.prepared-statements.php
$database =include __DIR__.'/config/database.php';
//http://www.runoob.com/php/php-pdo.html
 $dsn = 'mysql:dbname='.$database['database'].';host='.$database['hostname'];  
 $user = $database['username'];  
 $password = $database['password'];  

 $page = '-1';  
 $limit = '99999990'; 
 try {  
    $dbCon = new PDO($dsn, $user, $password);  
 } catch (PDOException $e) {  
    print 'Connection failed: '.$e->getMessage();  
    exit;  
 }  
 $xp_userlogon = $dbCon->prepare('call gettj_userpostbypage(:_pageindex,:_pagelimit)');  
 $xp_userlogon->bindParam(':_pageindex',  $page);
 $xp_userlogon->bindParam(':_pagelimit',  $limit); 
 $xp_userlogon->execute();  
 $uCol = $xp_userlogon->columnCount();  
 //echo $uCol."<br>";  
 $resultarray=[];
 while($row = $xp_userlogon->fetch()){  
  for( $i=0; $i<$uCol; $i++ )  
    unset($row[$i]);
   //print $row[$i]." ";  
    array_push($resultarray,$row);
    //var_dump($row);
  //print "<br>";  
 }
 $isfirst=1;
 if(count($resultarray)>0){
    $firstarray=$resultarray[0];
    ob_end_flush();
    foreach($firstarray as $key=>$value)
    {
       echo $key.",";
    }
    echo PHP_EOL;//"\r";
    foreach ($resultarray as $item) {
        foreach($item as $key2=>$value2)
        {
            echo $value2.",";
        }
        echo PHP_EOL;//"\r";
        flush();
    }
 }else{
     echo '暂无记录';
 }
//echo json_encode( $resultarray); http://www.jb51.net/article/68159.htm 
// header('Refresh: 3; url=http://www.baidu.com/'); //延迟转向 也就是隔几秒跳转
// $list = array();  
// for($i = 0 ; $i < 100 ; ++$i){  
//    for($j = 0 ; $j < 100 ; ++$j){  
//         $list[] = array($i,$j,$i+$j,$i-$j,$i*$j);  
//     }
// }   
// ob_end_flush();  
// foreach($list as $rs)  
// {  
//     echo $rs[0].",".$rs[1].",".$rs[2].",".$rs[3].",".$rs[4]."\r";  
//     flush();  
// }
?> 
?>  