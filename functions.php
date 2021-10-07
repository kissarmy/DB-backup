<?php

function redirect($adresa) {
    return "\n<!--meta http-equiv='refresh' content='1;url=".$adresa."'-->\n
    <script>window.location.href='".$adresa."';</script>\n";
;};


function redirectTimer($url = null, $timeMiliSeconds = null){
  return "<script>window.setTimeout(function(){window.location.href = '".$url."';}, ".$timeMiliSeconds.");</script>";
;}
   


function getDbArray(){
    if(!file("db.php")){file_put_contents("db.php", '<?php define("CONFIG_DB_ARRAY", "");?>');};
    if(file("db.php")){require_once("db.php");};  
    return json_decode(CONFIG_DB_ARRAY,  JSON_OBJECT_AS_ARRAY);;
;};

function saveDbArray($db_array_new){
    $db_array_new = json_encode($db_array_new, JSON_INVALID_UTF8_IGNORE);
    file_put_contents("db.php", '<?php define(\'CONFIG_DB_ARRAY\',\''.$db_array_new.'\');?>');
;};


function getDbDataById($id){
    $i=1;
    $db="";
    $db_array = getDbArray();
    foreach($db_array as $db2){
      if($id==$i){$db=$db2;};
      $i++;
    ;};
    return $db;
;};


function sqlFileToZip($file){
    $zip = new ZipArchive();
    $zip->open($file.'.zip',  ZipArchive::CREATE);
    $zip->addFile($file, end(explode("/", $file)));    
    $zip->close();
    if(end(explode(".", $file))=="sql"){unlink($file);};
    return $file.'.zip';
;};




function listBackupsPerDb($server, $dbName){
    $files="";
    $i=0;
    $server = str_replace(".", "-", str_replace(" ", "", $server));
    foreach (array_reverse(glob("backup/*_".$server."_".$dbName."_*.*")) as $file) {
        if($i==1){$files.="<div title='Show/Hide other files' onclick='showHide(\"files_".md5($server.$dbName)."\");' style='cursor:pointer;font-size:9px;'>Show/Hide other files</div><div style='display:none;' id='files_".md5($server.$dbName)."'>";};

        $files.=date("Y.m.d H:i:s", filemtime($file))." <small><small><i>(".getFileSize($file).")</i></small></small>
        <a href='$file' title='Download file'><img src='img/download.png' class='icon' /></a>
        <a href='?delete_file=".urlencode($file)."' title='Delete file' onclick='return confirm(\"Delete file: $file?\")'><img src='img/delete.png' class='icon' /></a>
        <BR>";
        $i++;
    ;};
    if($i>=1){$files.="<div>";};
    return $files;
;};



function textFilter($text){
    // ponechá mezery a velká a malá písmena
    $prevodni_tabulka = Array(
        ">"=>"",
        "<"=>"",
        ";"=>"",
        "'"=>"",
        '"'=>"",
        "$"=>"",
        /*"("=>"",*/
       /*")"=>"",*/
        "["=>"",
        "]"=>"",
        "{"=>"",
        "}"=>"",
        "\n"=>"",
        /*"\r"=>"",*/
        "\t"=>"",
        "^"=>"",
        "\\"=>"/",
        "\t"=>"", /* tabulátor */
      );      
      $text = strtr($text, $prevodni_tabulka);
    return $text;
;};

function textFilterArray($pole){  
    foreach ($pole as $nazev => &$hodnota) {
      if(is_array($pole[$nazev])){$novepole[$nazev] = textFilter($pole[$nazev]);} else {
      $novepole[$nazev] = textFilter($hodnota);};
    ;};
  return $novepole;
;};



function getFileSize($soubor){
    $bytes = filesize($soubor);
    return conversationFileSize($bytes);
;};
  
  function conversationFileSize($bytes){
          if ($bytes >= 1073741824)
          {
              $bytes = number_format($bytes / 1073741824, 2) . ' GB';
          }
          elseif ($bytes >= 1048576)
          {
              $bytes = number_format($bytes / 1048576, 2) . ' MB';
          }
          elseif ($bytes >= 1024)
          {
              $bytes = number_format($bytes / 1024, 2) . ' KB';
          }
          elseif ($bytes > 1)
          {
              $bytes = $bytes . ' bytes';
          }
          elseif ($bytes == 1)
          {
              $bytes = $bytes . ' byte';
          }
          else
          {
              $bytes = '0 bytes';
          }
  
          return $bytes;
;};













function backupDbTables($host, $user, $pass, $dbname, $tables = '*') {
    global $config;
    $link = mysqli_connect($host,$user,$pass, $dbname);

    // Check connection
    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }

    mysqli_query($link, "SET NAMES 'utf8'");

    //get all of the tables
    if($tables == '*')
    {
        $tables = array();
        $result = mysqli_query($link, 'SHOW TABLES');
        while($row = mysqli_fetch_row($result))
        {
            $tables[] = $row[0];
        }
    }
    else
    {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }

    $return = '';
    //cycle through
    foreach($tables as $table)
    {
        $result = mysqli_query($link, 'SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);
        $num_rows = mysqli_num_rows($result);

        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
        $counter = 1;

        //Over tables
        for ($i = 0; $i < $num_fields; $i++) 
        {   //Over rows
            while($row = mysqli_fetch_row($result))
            {   
                if($counter == 1){
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                } else{
                    $return.= '(';
                }

                //Over fields
                for($j=0; $j<$num_fields; $j++) 
                {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j<($num_fields-1)) { $return.= ','; }
                }

                if($num_rows == $counter){
                    $return.= ");\n";
                } else{
                    $return.= "),\n";
                }
                ++$counter;
            }
        }
        $return.="\n\n\n";
    }

    //save file
    //$fileName = 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
    $server = str_replace(".", "-", str_replace(" ", "", $host));
    $fileName = "backup/".date("YmdHis")."_".$server."_".$dbname."_".md5($host.$dbname.$user.$pass).".sql";
    $handle = fopen($fileName,'w+');
    fwrite($handle,$return);
    if(fclose($handle)){
        //echo "Done, the file name is: ".$fileName;
        if($config["backup_zip"]==1){$fileName = sqlFileToZip($fileName);}; //sql file to zip
        if(file($fileName)){return $fileName;};
        exit; 
    }
    
    mysqli_close($link);
;};




;?>