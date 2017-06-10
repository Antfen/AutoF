<?php
error_reporting(0);
include("../../ssf-wp-inc/includes/ssf-wp-env.php");
if(isset($_POST['img']))
{
	 $dir=SSF_WP_UPLOADS_PATH."/images/icons/";
	 if($_POST['img']=='a')
	 {
		unlink($dir.'custom-marker.png');
	 }
	 else if($_POST['img']=='b')
	 {
		unlink($dir.'custom-marker-active.png'); 
	 }
}

 if(isset($_POST['img_mrk']))
 {
 
   $MarkerDir=SSF_WP_UPLOADS_PATH.'/images/icons/'.$_POST['img_mrk'];
   
			if (is_dir($MarkerDir)){
				$images = @scandir($MarkerDir);
				foreach($images as $k=>$v):
				endforeach;
				unlink($MarkerDir.'/'.$v);
				rmdir($MarkerDir);
			}
 
 }
 else
 {   
    $dir=SSF_WP_UPLOADS_PATH.'/images/'.$_POST['img'];
			if (is_dir($dir)){
				$images = @scandir($dir);
				foreach($images as $k=>$v):
				endforeach;
				unlink($dir.'/'.$v);
				rmdir($dir);
			}
 }
?>
