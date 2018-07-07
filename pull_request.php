<?php
$cluster   = Cassandra::cluster()                
->withContactPoints('192.168.1.11')
->build();
$keyspace  = 'sampledb';
$session   = $cluster->connect($keyspace);

$list_file = array();
$get_file = $session->execute("SELECT file_id FROM file_chunk");
foreach($get_file as $row){
    if (!in_array($row['file_id'], $list_file)){
        array_push($list_file, $row['file_id']);
    }
}
// foreach($list_file as $file_id){
//     print ($file_id);
// }
echo json_encode($list_file);

?>