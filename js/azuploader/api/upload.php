<?php
   require_once '../config/config.php';

   $response = array();

   $ABSOLUTE_PATH = (isset($_GET['ABSOLUTE_PATH'])) ? $_GET['ABSOLUTE_PATH'] : ABSOLUTE_PATH;
   $RELATIVE_PATH = (isset($_GET['RELATIVE_PATH'])) ? $_GET['RELATIVE_PATH'] : RELATIVE_PATH;

   function readAllFiles($ABSOLUTE_PATH = null, $RELATIVE_PATH = null) {
      $ABSOLUTE_PATH = ($ABSOLUTE_PATH)? $ABSOLUTE_PATH : ABSOLUTE_PATH;
      $RELATIVE_PATH = ($RELATIVE_PATH) ? $RELATIVE_PATH : RELATIVE_PATH;

      $result = array();

      if ($handle = opendir($ABSOLUTE_PATH)) {
         while (false !== ($entry = readdir($handle))) {

            if ($entry != "." && $entry != "..") {

               $ext = pathinfo($entry, PATHINFO_EXTENSION);
               $data1 = array();

               $data['name'] = $entry;

               if ($ext === 'png' || $ext === 'PNG' || $ext === 'jpg' || $ext === 'JPG') {

                  $data['loc'] =  $RELATIVE_PATH . "/". $entry;
                  $data['type'] = 'image';

               } elseif (is_dir($ABSOLUTE_PATH . "\\" . $entry)) {

                  $data['loc'] = "ABSOLUTE_PATH=" . $ABSOLUTE_PATH . "\\" . $entry . "&RELATIVE_PATH=" . $RELATIVE_PATH . "/" . $entry;
                  $data['type'] = 'folder';

               } else {

                  $data['type'] = null;

               }

               array_push($result, $data);
           }
        }
        closedir($handle);
     }

     return $result;
   }

   if ($_FILES['files']['name']) {
      for($i=0; $i<count($_FILES['files']['name']); $i++) {

         $tmpFilePath = $_FILES['files']['tmp_name'][$i];

         if ($tmpFilePath != ""){
            $location = rtrim($_GET['ABSOLUTE_PATH'], '\\') . '\\';

            $newFilePath = $location . $_FILES['files']['name'][$i];

            if(move_uploaded_file($tmpFilePath, $newFilePath)) {

            }
         }
      }
   }

   $response['target'] = 'azlist-files';
   $response['files'] = readAllFiles($ABSOLUTE_PATH, $RELATIVE_PATH);
   $response['position'] = "ABSOLUTE_PATH=" . $ABSOLUTE_PATH . "&RELATIVE_PATH=" . $RELATIVE_PATH;

   echo json_encode($response);
?>
