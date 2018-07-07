<?php
$cluster   = Cassandra::cluster()                
->withContactPoints('192.168.1.11')
->build();
$keyspace  = 'sampledb';
$session   = $cluster->connect($keyspace);
$file_id = $_POST['file_id'];
$limit = 6;

$array_result = array();

$query_amount = $session->execute(new Cassandra\SimpleStatement ("SELECT chunks as count FROM chunk_count WHERE file_id =".$file_id." ALLOW FILTERING"));
$amount = 0;
foreach($query_amount as $row) {
    $amount = $row['count'];
}
$output = "";
if ($amount > $limit){
    $start = 0;
    $end = $limit;
    $flag = 0;
    while ($end <= $amount && $flag < 2) {
        $result = $session->execute("SELECT id, blobAsascii(chunk) as chunk FROM file_chunk WHERE file_id = ".$file_id." AND id >= ".(string)$start." AND id < ".(string)$end." ALLOW FILTERING");
        foreach($result as $row) {
            $output .= $row['chunk'];
        }
        $start = $end;
        if (($end + $limit) > $amount){
            $end = $amount;
            $flag += 1;
        } else {
            $end += $limit;
        }
    }
} else {
    $result = $session->execute("SELECT id, blobAsascii(chunk) as chunk FROM file_chunk WHERE file_id = ".$file_id. " ALLOW FILTERING");
        foreach($result as $row) {
            $output .= $row['chunk'];
        }
}
$output = pack("H*", $output);
echo "data:image/png;base64, ".base64_encode($output);

?>