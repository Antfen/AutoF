<?php
// include config file
include_once './includes/config.inc.php';
include_once './includes/validate.php';

validate_request_add_store();


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>Super Store Finder - Add Store</title>
	<?php include ROOT."settings.php"; ?>
	<?php include ROOT."themes/meta_others.php"; ?>
</head>
<body id="add_edit_body">
	<div id="wrapper">
		<div id="header">
			
			<?php include ROOT."themes/nav.inc.php"; ?>
		</div>

		<div id="main">
			<h2><?php echo $lang['ADMIN_ADD_STORE']; ?></h2>
			<p>
				<?php echo $lang['REQUEST_STORE_ADMIN_APPROVAL']; ?>
			</p>
			
			<?php echo notification(); ?>
			<?php if(isset($errors)): ?>
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
			<form method='post' id='form_new_store' enctype="multipart/form-data" onSubmit="getCategories(); return false;">
			
				<fieldset>
					<legend><?php echo $lang['ADMIN_ADD_STORE']; ?></legend>
					
						<label><?php echo $lang['ADMIN_NAME']; ?>: <span class='required'>*</span></label>
						<input type='text' name='name' id='name' value='<?php echo $fields['name']['value']; ?>' />
						
						<?php 
						$db = db_connect();
						mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");
						$cats = $db->get_rows("SELECT categories.* FROM categories WHERE categories.id!='' ORDER BY categories.cat_name ASC");

						?>
						
						<label>Category: <span class='required'>*</span></label>
						<select name="cat_id" class="form-select" id="cat_id" multiple="multiple">
						 <?php if(!empty($cats)): ?>
							<?php foreach($cats as $k=>$v): ?>
							<option value="<?php echo $v['id']; ?>"><?php echo $v['cat_name']; ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						 </select>
					
						<label><?php echo $lang['ADMIN_ADDRESS']; ?>: <span class='required'>*</span></label>
						<input type='text' name='address' id='address' value='<?php echo $fields['address']['value']; ?>' />
						<span><?php echo $lang['ADMIN_LAT_LANG_AUTO']; ?></span>
					
					
						<label><?php echo $lang['ADMIN_TELEPHONE']; ?>:</label>
						<input type='text' name='telephone' id='telephone' value='<?php echo $fields['telephone']['value']; ?>' />
					
					
						<label><?php echo $lang['ADMINISTRATOR_EMAIL']; ?>:</label>
						<input type='text' name='email' id='email' value='<?php echo $fields['email']['value']; ?>' />
					
					
						<label><?php echo $lang['ADMIN_WEBSITE']; ?>:</label>
						<input type='text' name='website' id='website' value='<?php echo $fields['website']['value']; ?>' />
					
						<label><?php echo $lang['ADMIN_DESCRIPTION']; ?>:</label>
						<textarea name='description' id='description' rows="5" cols="40"><?php echo $fields['description']['value']; ?></textarea>
					
						<label><?php echo $lang['ADMIN_STORE_IMAGE']; ?>:</label>
						<input type="file" name="file" id="file" />
						<span><?php echo $lang['ADMIN_THUMB_AUTO']; ?> </span>
					

					
					<?php if(!empty($images)): ?>
					<div class="input">
						<?php foreach($images as $k=>$v): ?>
						<div class="image">
							<img src="<?php echo $v; ?>" alt="Image" />
							<button type="submit" name="delete_image[<?php echo basename($v); ?>]" id="delete_image" class="btn btn-danger" value="<?php echo basename($v); ?>"><?php echo $lang['ADMIN_DELETE']; ?></button>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					
					<div class='input first'>
						<label><?php echo $lang['ADMIN_LATITUDE']; ?>:</label>
						<input type='text' name='latitude' id='latitude' value='<?php echo $fields['latitude']['value']; ?>' />
					</div>
					<div class='input second'>
						<label><?php echo $lang['ADMIN_LONGITUDE']; ?>:</label>
						<input type='text' name='longitude' id='longitude' value='<?php echo $fields['longitude']['value']; ?>' />
					</div>
					
					
					<div class='input buttons'>
						<button type='submit' name='save' class="btn" id='save'><?php echo $lang['ADMIN_SAVE']; ?></button>
					</div>
				</fieldset>
				<input type="hidden" name="catlist" id="catlist" />
			</form>
			</div>
			
		</div>
	</div>

	<script>
			
			$(function() {
				$('#cat_id').multiselect({
				includeSelectAllOption: true
				});
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
				valForm();
			}
			
			function valForm(){
			error = 0;
			resetForm();

				if($('#name').val()==""){
					$('#val-name').addClass("error");
					$('#text-name').html('<?php echo $lang['ADMIN_STORE_NAME_VALIDATE']; ?>');
					error = 1;
				}
				
				if($('#address').val()==""){
					$('#val-address').addClass("error");
					$('#text-address').html('<?php echo $lang['ADMIN_STORE_ADDRESS_VALIDATE']; ?>');
					error = 1;
				}
				
				if($('#latitude').val()==""){
					$('#val-latitude').addClass("error");
					$('#text-latitude').html('<?php echo $lang['ADMIN_STORE_LATITUDE_VALIDATE']; ?>');
					error = 1;
				}
				
				if($('#longitude').val()==""){
					$('#val-longitude').addClass("error");
					$('#text-longitude').html('<?php echo $lang['ADMIN_STORE_LONGITUDE_VALIDATE']; ?>');
					error = 1;
				}
				
				if($('#email').val()!=""){
				if(!validateEmail($('#email').val())){
					$('#val-email').addClass("error");
					$('#text-email').html('<?php echo $lang['ADMIN_STORE_EMAIL_VALIDATE']; ?>');
					error = 1;
				}
				}
				
				
				tel = $('#telephone').val();
				if(tel!=""){
				if((!tel.match(/^\d+/))){
					$('#val-telephone').addClass("error");
					$('#text-telephone').html('<?php echo $lang['ADMIN_STORE_TELEPHONE_VALIDATE']; ?>');
					error = 1;
				}
				}
				
				if(error==0){
				   document.f.submit();
				}


			}

			function resetForm(){
				$('#text-name').html('');
				$('#text-address').html('');
				$('#text-latitude').html('');
				$('#text-longitude').html('');
				$('#text-telephone').html('');
				$('#text-email').html('');

				$('#val-name').removeClass("error");
				$('#val-address').removeClass("error");
				$('#val-latitude').removeClass("error");
				$('#val-longitude').removeClass("error");
				$('#val-telephone').removeClass('');
				$('#val-email').removeClass('');

			}

			function validateEmail(email) { 
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email);
			} 
			
			
	</script>


<?php include ROOT."themes/footer.inc.php"; ?>
</body>
</html>