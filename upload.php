<?php
$cluster   = Cassandra::cluster()                
->withContactPoints('192.168.0.113')
->build();
$keyspace  = 'sampledb';
$session   = $cluster->connect($keyspace); 
$file = $_FILES['file']['tmp_name'];
$buffer = 1024 * 60;
$file_handle = fopen($file,'r');
//get file size
$file_size = filesize($file);
//no of parts to split
$parts = $file_size / $buffer;
//store all the file names
$file_parts = array();

//name of input file
$file_name = basename($file);
$file_id = new Cassandra\Uuid();
for($i=0;$i<$parts;$i++){
    //read buffer sized amount from file
    $file_part = fread($file_handle, $buffer);
    //the filename of the part
    $file_part_path = $i;
    //upload file part to server
    $file_chunk = bin2hex($file_part);
    $insert = $session->prepare(
        "INSERT INTO file_chunk (file_id, id, chunk) " .
        "VALUES (?, ?, asciiAsBlob('$file_chunk'))"
    );   
    $id_gen = array(
        'file_id' => $file_id,
        'id' => $file_part_path
    );
    $options = array('arguments' => $id_gen);
    $session->execute($insert, $options);
}    
//close the main file handle

fclose($file_handle);
echo $file_id;
?>
