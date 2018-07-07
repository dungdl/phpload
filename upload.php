<?php
$cluster   = Cassandra::cluster()                
->withContactPoints('192.168.1.11')
->build();
$keyspace  = 'sampledb';
$session   = $cluster->connect($keyspace); 
$file = $_FILES['file']['tmp_name'];
$file_handle = fopen($_FILES["file"]["tmp_name"], 'r');
//get file size
$file_size = filesize($file);
$buffer = 600 * 1024;
//number of parts to split
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
    
    // $insert = $session->prepare(
    //     "INSERT INTO file_chunk (file_id, id, chunk) " .
    //     "VALUES (?, ?, asciiAsBlob('$file_chunk'))"
    // );   
    // $id_gen = array(
    //     'file_id' => $file_id,
    //     'id' => $file_part_path
    // );
    // $options = array('arguments' => $id_gen);
    // $session->execute($insert, $options);
    try {
        $session->execute(
            "INSERT INTO file_chunk (file_id, id, chunk) " .
            "VALUES (".$file_id.", ".$file_part_path.", asciiAsBlob('$file_chunk'))");
        $session->execute(new Cassandra\SimpleStatement(
            "UPDATE chunk_count
                SET chunks = chunks + 1
                WHERE file_id = ".$file_id));

    } catch (Cassandra\Exception $ex) {
        echo "This is: ".$i." Among: ".$parts."| ";
        echo $ex;
        break;
    }
}    
//close the main file handle

fclose($file_handle);
echo $file_id;
?>
