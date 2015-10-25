<?php
   require 'js/azuploader/config/config.php';

   $ABSOLUTE_PATH = isset($_GET['ABSOLUTE_PATH'])? $_GET['ABSOLUTE_PATH'] : ABSOLUTE_PATH;
   $FOLDER_PATH = isset($_GET['FOLDER_PATH']) ? $_GET['FOLDER_PATH'] : FOLDER_PATH;

   $result = array();

   if ($handle = opendir($ABSOLUTE_PATH)) {
      $last_letter  = $ABSOLUTE_PATH[strlen($ABSOLUTE_PATH)-1];
      $last_letterF  = $FOLDER_PATH[strlen($FOLDER_PATH)-1];
      $root  = ($last_letter == '\\' || $last_letter == '/') ? $ABSOLUTE_PATH : $ABSOLUTE_PATH.DIRECTORY_SEPARATOR;
      $fld = ($last_letterF == '/') ? $FOLDER_PATH : $FOLDER_PATH . '/';
      $directories[]  = array( 'ABSOLUTE_PATH' => $root , 'FOLDER_PATH' => $fld);

      while (sizeof($directories)) {
         $dir  = array_pop($directories);
         if ($handle = opendir($dir['ABSOLUTE_PATH'])) {
            echo "string <br>";
            while (false !== ($file = readdir($handle))) {
               if ($file == '.' || $file == '..') continue;

               $location  = $dir['ABSOLUTE_PATH'].$file;
               $locationfld = $dir['FOLDER_PATH'] . $file;

               if (is_dir($location)) {
                  $data = array();
                  $directory_path = $location.DIRECTORY_SEPARATOR;
                  $folder_path = $locationfld . "/";
                  echo $file . "<br>";
                  $data['name'] = $file;
                  $data['loc'] = "?ABSOLUTE_PATH=" . rtrim($directory_path , '\\') . "&FOLDER_PATH=" .  rtrim($folder_path, '/');
                  $data['type'] = 'folder';

                  array_push($directories, array( 'ABSOLUTE_PATH' => $directory_path , 'FOLDER_PATH' => $folder_path));

                  array_push($result, $data);
               }
            }
            closedir($handle);
         }
      }
   }

   var_dump($result);
?>
