<?php
// include Config File
include_once './includes/config.inc.php';
include_once './includes/validate.php';
// Authenticate user login
auth();
$upload_dir = '';
validate_edit_store();

$images = array();
	if(is_dir($upload_dir)) {
		$images = get_files($upload_dir);
		foreach($images as $k=>$v) {
			$images[$k] = ROOT_URL.'imgs/stores/'.$store['id'].'/'.$v;
		}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $lang['STORE_FINDER']; ?> - <?php echo $lang['ADMIN_EDIT_STORE']; ?></title>
	<?php include 'header.php'; ?>
	<script class="jsbin" src="js/upload.js"></script>
</head>
<body id="add_edit_body">
	<div id="wrapper">
		<div id="header">
			
			<?php include 'nav.php'; ?>
		</div>
		<div id="main">
			<h2><?php echo $lang['ADMIN_EDIT_STORE']; ?></h2>
			<?php echo notification(); ?>
			<?php if(isset($errors) && !empty($errors)): ?>
			<div class="alert alert-block alert-error fade in">
			<ul>
				<?php foreach($errors as $k=>$v): ?>
				<li><?php echo $v; ?></li>
				<?php endforeach; ?>
			</ul>
			</div>
			<?php endif; ?>


			<div id="map_canvas" class="newstore_map"></div>
			<div id="ajax_msg"></div>

			<div style="display:block; clear:both">
			<form method='post' action='./stores_edit.php?id=<?php echo $store['id']; ?>' id='form_new_store' name='form_new_store' enctype="multipart/form-data" onSubmit="getCategories(); return false;">
				<fieldset>
					<legend><?php echo $lang['ADMIN_EDIT_STORE']; ?></legend>
					
						<label><?php echo $lang['ADMIN_NAME']; ?>: <span class='required'>*</span></label>
						<input type='text' name='name' id='name' value="<?php echo $fields['name']['value']; ?>" />
						
						<?php 
						$db = db_connect();
						mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");
						$cats = $db->get_rows("SELECT categories.* FROM categories WHERE categories.id!='' ORDER BY categories.cat_name ASC");
						$ex_cat = explode(',',$store['cat_id']);
						
						?>
						
						<label><?php echo $lang['SSF_CATEGORY']; ?>: <span class='required'>*</span></label>
						<select name="cat_id" class="form-select" id="cat_id" multiple="multiple">
						 <?php if(!empty($cats)): ?>
							<?php foreach($cats as $k=>$v): ?>
							<option value="<?php echo $v['id']; ?>" <?php if(in_array($v['id'], $ex_cat)){ ?>selected<?php } ?>><?php echo $v['cat_name']; ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						 </select>
					
						<label><?php echo $lang['ADMIN_ADDRESS']; ?>: <span class='required'>*</span></label>
						<input type='text' name='address' id='address' value="<?php echo $fields['address']['value']; ?>" />
						<span><?php echo $lang['ADMIN_LAT_LANG_AUTO']; ?></span>
					
						<label><?php echo $lang['ADMIN_TELEPHONE']; ?>:</label>
						<input type='text' name='telephone' id='telephone' value='<?php echo $fields['telephone']['value']; ?>' />
					
						<label><?php echo $lang['ADMINISTRATOR_EMAIL']; ?>:</label>
						<input type='text' name='email' id='email' value='<?php echo $fields['email']['value']; ?>' />
					
						<label><?php echo $lang['ADMIN_WEBSITE']; ?>:</label>
						<input type='text' name='website' id='website' value='<?php echo $fields['website']['value']; ?>' />
					
						<label><?php echo $lang['ADMIN_DESCRIPTION']; ?>:</label>
						<textarea name='description' id='description' rows="5" cols="40"><?php echo $fields['description']['value']; ?></textarea>
					
					<label>Embed Video:</label>
						<input type='text' name='embed_video' id='embed_video' value='<?php echo $fields['embed_video']['value']; ?>' />
						
						<label><?php echo $lang['ADMIN_STORE_IMAGE']; ?>:</label>
						<input type="file" name="file" id="file" <?php if(!empty($images)) {echo 'disabled="disabled"';} ?>/>
						<span><?php echo $lang['ADMIN_THUMB_AUTO']; ?></span>
					  <div class="input">
						<div class="image">
						<div id="img"></div>
						<input id="width" type="hidden" value="100" />
						<input id="height" type="hidden" />
						</div>
						</div>
					
					
					<?php if(!empty($images)): ?>
					<div class="input">
						<?php foreach($images as $k=>$v): ?>
						<div class="image">
							<img src="<?php echo $v; ?>" alt="Image" />
							<button type="submit" class="btn btn-danger" name="delete_image[<?php echo basename($v); ?>]" id="delete_image" value="<?php echo basename($v); ?>">Delete</button>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					
					<br /><br />
					<label>Default Media:</label>
						<input type='radio' name='default_media' id='default_media' value='image' <?php if($fields['default_media']['value']=='image' || $fields['default_media']['value']=='') { echo "checked"; } ?>/> Image <br />
						<input type='radio' name='default_media' id='default_media' value='video' <?php if($fields['default_media']['value']=='video') { echo "checked"; } ?>/> Video <br /><br />
					
					<div class='input first'>
						<label><?php echo $lang['ADMIN_LATITUDE']; ?>:</label>
						<input type='text' name='latitude' id='latitude' value='<?php echo $fields['latitude']['value']; ?>' />
					</div>
					<div class='input second'>
						<label><?php echo $lang['ADMIN_LONGITUDE']; ?>:</label>
						<input type='text' name='longitude' id='longitude' value='<?php echo $fields['longitude']['value']; ?>' />
					</div>
				

					<div class='input buttons'>
						<button type='submit' class="btn" name='save' id='save'><?php echo $lang['ADMIN_SAVE']; ?></button>
						<button type='button' class="btn" onclick="document.location.href='stores.php'"><?php echo $lang['ADMIN_CANCEL']; ?></button>
					</div>
				</fieldset>
				

				<input type="hidden" name="catlist" id="catlist" />
				</form>
				</div>

		</div>
	</div>
	<?php include '../themes/footer.inc.php'; ?>
	
	<script>
$(document).ready(function() {
	$('#cat_id').multiselect();
});


function getCategories()
{

	 var selectedCat = "";
	 var x = 0;

	 
	var catlist = []; 
	$('#cat_id :selected').each(function(i, selected){ 
	catlist[i] = $(selected).val(); 
	});
	
	for (x=0;x<catlist.length;x++)
	{
		if(x==0){
		 selectedCat = catlist[x];
		} else {
		 selectedCat = selectedCat + "," + catlist[x];
		} 
	}
	$('#catlist').val(selectedCat);
	document.f.submit();
}
 
</script>
<script class="jsbin" src="js/imageScript.js"></script>
</body>
</html>