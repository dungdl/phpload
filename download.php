<?php
$cluster   = Cassandra::cluster()                
->withContactPoints('192.168.0.113')
->build();
$keyspace  = 'sampledb';
$session   = $cluster->connect($keyspace);
$file_id = $_POST['file_id'];
$result = $session->execute(new Cassandra\SimpleStatement
          ("SELECT id, blobAsascii(chunk) as chunk FROM file_chunk WHERE file_id = ".$file_id."ALLOW FILTERING"));
$file_array = array();
foreach($result as $row) {
    $file_array[$row['id']] = $row['chunk']; 
}
ksort($file_array);
$file_final = "";
foreach ($file_array as $i => $value) {
    $file_final .= $file_array[$i];
}
$file_final = pack("H*", $file_final);
echo "data:image/png;base64,".base64_encode($file_final);
?>