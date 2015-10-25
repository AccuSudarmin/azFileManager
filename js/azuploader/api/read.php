<?php
   if (!isset($_GET['request'])) exit();
   require_once '../config/config.php';

   $response = array();

   $ABSOLUTE_PATH = (isset($_GET['ABSOLUTE_PATH'])) ? $_GET['ABSOLUTE_PATH'] : ABSOLUTE_PATH;
   $RELATIVE_PATH = (isset($_GET['RELATIVE_PATH'])) ? $_GET['RELATIVE_PATH'] : RELATIVE_PATH;

   function readAllFolder($root = '.'){
      $folder  = array();
      $directories  = array();

      $last_letterAbsolute  = $root[strlen($root)-1];
      $last_letterRoot = $root[strlen($root)-1];

      $root  = ($last_letterAbsolute == '\\') ? $root : $root.DIRECTORY_SEPARATOR;

      $directories[]  = $root;

      while (sizeof($directories)) {
         $dir  = array_pop($directories);
         if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
               if ($file == '.' || $file == '..') continue;

               $location  = $dir.$file;
               if (is_dir($location)) {
                  $directory_path = $location.DIRECTORY_SEPARATOR;
                  array_push($directories, $directory_path);
                  // $files['dirs'][]  = $directory_path;
                  array_push($folder, array('name' => $file, 'location'=> $location));
               }
            }
            closedir($handle);
         }
      }

     return $folder;
   }

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

               if ($ext === 'png' || $ext === 'PNG' || $ext === 'jpg' || $ext === 'JPG' ) {

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

   function getCurrentFolder($ABSOLUTE_PATH = null, $RELATIVE_PATH = null){
      $ABSOLUTE_PATH = ($ABSOLUTE_PATH) ? $ABSOLUTE_PATH : ABSOLUTE_PATH;
      $RELATIVE_PATH = ($RELATIVE_PATH) ? $RELATIVE_PATH : RELATIVE_PATH;

      $listpath = array();

      $oldpath = explode('\\', rtrim(ABSOLUTE_PATH, "\\"));
      $newpath = explode('\\', rtrim($ABSOLUTE_PATH, "\\"));
      $newpathrelative = explode('/', rtrim($RELATIVE_PATH, "/"));

      if ($newpath >= $oldpath) {
         $numnewpath = count($newpath);
         $numoldpath = count($oldpath);
         for ($i=$numnewpath-1; $i >= $numoldpath-1; $i--) {

            $loc = implode('\\', array_slice($newpath, 0, $i+1));

            $name = $newpath[$i];
            array_push($listpath, array('name' => $name, 'loc' => "ABSOLUTE_PATH=" . $loc . "&RELATIVE_PATH=" . implode('/', $newpathrelative)));
            array_pop($newpathrelative);
         }
      }

      return $listpath;
   }

   switch ($_GET['request']) {
      case 'allfiles':
         $response['target'] = 'azlist-files';
         $response['files'] = readAllFiles($ABSOLUTE_PATH, $RELATIVE_PATH);
         $response['position'] = "ABSOLUTE_PATH=" . $ABSOLUTE_PATH . "&RELATIVE_PATH=" . $RELATIVE_PATH;
         break;

      case 'navigation':
         $response['target'] = 'navigation';
         $response['navigation'] = getCurrentFolder($ABSOLUTE_PATH, $RELATIVE_PATH);
         break;

      case 'folder':
         $response['target'] = 'azlist-folder';
         $response['folder'] = readAllFolder(ABSOLUTE_PATH);
         break;
   }

   echo json_encode($response);

?>
