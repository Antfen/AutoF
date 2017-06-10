<?php error_reporting(0);
ini_set('display_errors', 0);
// Send the headers
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
// echo some dynamically generated content here
?>
<top xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<legend>
			<?php  include_once './includes/config.inc.php';
			// support unicode
			mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");
			$cats = $db->get_rows("SELECT categories.* FROM categories WHERE categories.id!='' ORDER BY categories.cat_name ASC");
			?>
 			<?php if(!empty($cats)): ?>
			<?php foreach($cats as $k=>$v): ?>
			<label>
			<tag>cat<?php echo $v['id']; ?></tag>
			<copy><?php echo $v['cat_name']; ?></copy>
		</label>
			<?php endforeach; ?>
			<?php endif; ?>
	</legend>

	<store>
	  	<?php 
		// support unicode
		mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");
		$stores = $db->get_rows("SELECT * FROM stores WHERE id!='' AND approved='1' AND latitude!='' AND latitude!='0' ORDER BY name ASC");
		
 function ssfParseToXML($htmlStr) 
{ 
	/*$xmlStr=str_replace('<','&lt;',$htmlStr); 
	$xmlStr=str_replace('>','&gt;',$xmlStr); 
	$xmlStr=str_replace('"','&quot;',$xmlStr); 
	$xmlStr=str_replace("'",'&#39;',$xmlStr); 
	$xmlStr=str_replace("&",'&amp;',$xmlStr); */
	$xmlStr=str_replace("<br>" ,"<br />" ,$htmlStr); 
	$xmlStr=str_replace('&nbsp;',' ',$xmlStr);
	return $xmlStr; 
} 

?>

 			<?php if(!empty($stores)): ?>
			<?php foreach($stores as $k=>$v): ?>
			<?php $v['name'] = str_replace('&',' &amp; ', $v['name']); ?>
			<?php $v['address'] = str_replace('&','&amp;', $v['address']); ?>
			<?php $v['website'] = str_replace('&','&amp;', $v['website']); ?>
			<?php $v['description'] = ssfParseToXML($v['description']); ?>
			<?php $v['openinghour'] = ssfParseToXML($v['openinghour']); ?>
			
			<item>			
			<location><?php echo $v['name']; ?></location>
			<address><?php echo $v['address']; ?></address>
			<description><?php echo $v['description']; ?></description>
			<latitude><?php echo $v['latitude']; ?></latitude>
			<longitude><?php echo $v['longitude']; ?></longitude>
			<embedvideo><?php echo base64_encode($v['embed_video']); ?></embedvideo>
			<operatingHours><?php echo $v['openinghour']; ?></operatingHours>
			<defaultmedia><?php echo $v['default_media']; ?></defaultmedia>
			<website><?php echo $v['website']; ?></website>
			<email><?php echo $v['email']; ?></email>
			<telephone><?php echo $v['telephone']; ?></telephone>
			<?php 
			$ex_cat= trim($v['cat_id'], ',');
			$ex_cat_arr = explode(',',$ex_cat);
				foreach ($ex_cat_arr as $catid) {?>
			<cat<?php echo $catid; ?>>true</cat<?php echo $catid; ?>>
			<?php
			}
			// store img
			$upload_dir = ROOT.'admin/imgs/stores/'.$v['id'].'/';
			$img = '';
			$images = array();
			if(is_dir($upload_dir)) {
				$images = get_files($upload_dir);
				foreach($images as $k=>$l) {
					$images[$k] = 'admin/imgs/stores/'.$v['id'].'/'.$l;
				}
		    }
			?>
				<?php if(!empty($images)): ?>
						<?php foreach($images as $k=>$l): ?>
						<?php
						if (strpos($l,'ori_') !== false) {
		
		                } else {
						$img = ROOT_URL."".$l;
						} ?>
						<?php endforeach; ?>
					<?php endif; ?>
			<?php
  echo '<storeimage>'.$img.'</storeimage>';
 $upload_dir_marker = ROOT.'admin/imgs/marker/'.$v['id'].'/';
			$files = get_files($upload_dir_marker);
			if(is_array($files)) {
				$files = array_values($files);
			}
			$marker = '';
			if($files !== FALSE && isset($files[0])) {
				$marker = ROOT_URL.'admin/imgs/marker/'.$v['id'].'/'.$files[0];
			}
 echo '<store_marker>'.$marker.'</store_marker>'; ?>
		</item>
<?php endforeach; ?>
<?php endif; ?>
		
	</store>
	</top>