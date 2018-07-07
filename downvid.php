<?php
$cluster   = Cassandra::cluster()                
->withContactPoints('192.168.0.120')
->build();
$keyspace  = 'sampledb';
$session   = $cluster->connect($keyspace);
$file_id = $_POST['file_id'];
$limit = 6;

$query_amount = $session->execute(new Cassandra\SimpleStatement ("SELECT chunks as count FROM chunk_count WHERE file_id =".$file_id." ALLOW FILTERING"));
$amount = 0;
foreach($query_amount as $row) {
    $amount = $row['count'];
}
$start = 0;
$end = $limit;
$output = fopen("F:\output.CR2","w");
$flag = 0;
while ($end <= $amount && $flag < 2) {
    $result = $session->execute("SELECT id, blobAsascii(chunk) as chunk FROM file_chunk WHERE file_id = ".$file_id." AND id >= ".(string)$start." AND id < ".(string)$end." ALLOW FILTERING");
    foreach($result as $row) {
        fwrite($output, hex2bin($row['chunk']));
    }
    $start = $end;
    if (($end + $limit) > $amount){
        $end = $amount;
        $flag += 1;
    } else {
        $end += $limit;
    }
}
fclose($output);
echo "done";
?>