<?php
   require 'config/config.php';

   $ABSOLUTE_PATH = isset($_GET['ABSOLUTE_PATH'])? $_GET['ABSOLUTE_PATH'] : ABSOLUTE_PATH;
   $FOLDER_PATH = isset($_GET['FOLDER_PATH']) ? $_GET['FOLDER_PATH'] : FOLDER_PATH;

   $result = array();

   if ($handle = opendir($ABSOLUTE_PATH)) {
      while (false !== ($entry = readdir($handle))) {

         if ($entry != "." && $entry != "..") {

            $ext = pathinfo($entry, PATHINFO_EXTENSION);
            $data1 = array();

            $data['name'] = $entry;

            if ($ext === 'png' || $ext === 'jpg' ) {

               $data['loc'] =  $FOLDER_PATH . "\\". $entry;
               $data['type'] = 'image';

            } elseif ($ext === '') {

               $data['loc'] = "?ABSOLUTE_PATH=" . $ABSOLUTE_PATH . "\\" . $entry . "&FOLDER_PATH=" . $FOLDER_PATH . "/" . $entry;
               $data['type'] = 'folder';

            } else {

               $data['type'] = null;

            }

            array_push($result, $data);
        }
     }

     closedir($handle);
   }

   echo json_encode($result);
?>
