<?php
// Do not show notice errors
error_reporting (E_ALL ^ E_NOTICE);

if(!empty($_FILES)) // [START FILE UPLOADED]
{
include 'config.php';

$file = $_FILES['image_file'];

$file_name = $file['name'];

$error = ''; // Empty

$done = '';

$file_uploaded = null;

$exif = [];

// Get File Extension (if any)

$ext = strtolower(substr(strrchr($file_name, "."), 1));

// Check for a correct extension. The image file hasn't an extension? Add one

   if($validation_type == 1)
   {
   $file_info = getimagesize($_FILES['image_file']['tmp_name']);

      if(empty($file_info)) // No Image?
      {
      $error .= "The uploaded file doesn't seem to be an image.";
      }
      else // An Image?
      {
      $file_mime = $file_info['mime'];

         if($ext == 'jpc' || $ext == 'jpx' || $ext == 'jb2')
         {
         $extension = $ext;
         }
         else
         {
         $extension = ($mime[$file_mime] == 'jpeg') ? 'jpg' : $mime[$file_mime];
         }

         if(!$extension)
         {
         $extension = '';
         $file_name = str_replace('.', '$image_extensions_allowed', $file_name);
         }
     }
   }
   else if($validation_type == 2)
   {
     if(!in_array($ext, $image_extensions_allowed))
     {
     $exts = implode(', ',$image_extensions_allowed);
     $error .= "You must upload a file with one of the following extensions: ".$exts;
     }

     $extension = $ext;
   }

   if($error == "") // No errors were found?
   {
   $new_file_name = strtolower($file_name);
   $new_file_name = str_replace(' ', '-', $new_file_name);
   $new_file_name = substr($new_file_name, 0, -strlen($ext));
   $new_file_name .= $extension;
   
   // File Name
   $move_file = move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$new_file_name);

   if($move_file)
      {
      $done = 'The image has been uploaded.';
      }
   }
   else
   {
   @unlink($file['tmp_name']);
   }

   $file_uploaded = true;
} // [END FILE UPLOADED]
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <title>Validate Image on Upload @ BitRepository.com</title>
  <meta name="Author" content="BitRepository.com">

  <meta name="Keywords" content="validate, image, upload, post, files">
  <meta name="Description" content="How to check if an uploaded file is an image">
  <style>
   img {
   display: block;
   margin-left: auto;
   margin-right: auto;
   }
   </style>
 </head>

 <body>

<center>

 <?php
  if($file_uploaded)
 {
   if($done)
   {
      print( '<font color="green">'.$done.'</font>');
      $filepath =  $upload_image_to_folder.$new_file_name;
      print "\r\n <img src=".$filepath." height=200 width=300 />";
      // $exif = exif_read_data($filepath);
      // foreach ($exif as $key => $section) {
      //    foreach ($section as $name => $val) {
      //       echo "$key.$name: $val<br />\n";
      //    }
      // }
      $output = shell_exec("php ./$filepath");
      //eval("$output = "$output";");
      echo "<pre>$output</pre>";
   }
   else if($error)
   {
      echo '<font color="red">'.$error.'</font>';
   }
   echo '<br /><br />';
 }
 ?>

<form enctype="multipart/form-data" action="validate_image_upload.php" method="POST">
    
<!-- MAX_FILE_SIZE must be set before the input element -->
<input type="hidden" name="MAX_FILE_SIZE" value="2048000" />

<!-- The name from the $_FILES array is determined by the input name -->
Select an Image: <input name="image_file" type="file" /> <input type="submit" value="Upload" />

</form>

</center>

 </body>
</html>