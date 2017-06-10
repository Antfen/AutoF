<?php
// include config file
include_once './includes/config.inc.php';

// list of available distances
$distances = array(
	200=>'200 Miles',
    100=>'100 Miles',
	50=>'50 Miles',
);


if(isset($_POST['ajax'])) {
	
	if(isset($_POST['action']) && $_POST['action']=='get_nearby_stores') {
		
		if(!isset($_POST['lat']) || !isset($_POST['lng'])) {
			
			echo json_encode(array('success'=>0,'msg'=>'Coordinate not found'));
		exit;
		}
		
		// support unicode
		mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");

		// category filter
		
		if (!isset($_POST['catlist']) || $_POST['catlist']=="" || strpos($_POST['catlist'],'multiselect-all') !== false) {
			$category_filter = "";
		} else {
		
			$arr_clist = explode(",",$_POST['catlist']);
			$multicatfilter = "";
			for($i=0; $i<sizeof($arr_clist); $i++){
				if($i==0){
					$multicatfilter .= "cat_id  LIKE '%,".$arr_clist[$i].",%' "; 
				} else {
					$multicatfilter .= "OR cat_id LIKE '%,".$arr_clist[$i].",%' ";
				}
			}

			$category_filter = " AND (".$multicatfilter.")";
		}
		$sql = $db->get_rows("SELECT *, ( 3959 * ACOS( COS( RADIANS(".$_POST['lat'].") ) * COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS(".$_POST['lng'].") ) + SIN( RADIANS(".$_POST['lat'].") ) * SIN( RADIANS( latitude ) ) ) ) AS distance FROM stores WHERE status=1 AND approved=1 ".$category_filter." HAVING distance <= ".$_POST['distance']." ORDER BY distance ASC LIMIT 0,60");	
		
		echo json_stores_list($sql);
	}
exit;
}


$errors = array();

if($_POST) {
	if(isset($_POST['address']) && empty($_POST['address'])) {
		$errors[] = 'Please enter your address';
	} else {

			
		$google_api_key = '';

		$region = 'us';

		$tmp = '';
		
		$xml = convertXMLtoArray($tmp);
		
		if($xml['Response']['Status']['code']=='200') {
			
			$coords = explode(',', $xml['Response']['Placemark']['Point']['coordinates']);
			
			if(isset($coords[0]) && isset($coords[1])) {
				
				$data = array(
					'name'=>$v['name'],
					'address'=>$v['address'],
					'latitude'=>$coords[1],
					'longitude'=>$coords[0]
				);

				
				$sql = "SELECT *, ( 3959 * ACOS( COS( RADIANS(".$coords[1].") ) * COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS(".$coords[0].") ) + SIN( RADIANS(".$coords[1].") ) * SIN( RADIANS( latitude ) ) ) ) AS distance FROM stores WHERE status=1 HAVING distance <= ".$db->escape($_POST['distance'])." ORDER BY distance ASC  LIMIT 0,60";
				
				$stores = $db->get_rows($sql);

				
				if(empty($stores)) {
					$errors[] = 'Stores with address '.$_POST['address'].' not found.';
				}
			} else {
				$errors[] = 'Address not valid';
			}
		} else {
			$errors[] = 'Entered address'.$_POST['address'].' not found.';
		}
	}
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $lang['STORE_FINDER']; ?></title>
	<?php include ROOT."settings.php"; ?>
    <?php include ROOT."themes/meta.php"; ?>
</head>
<body id="super-store-finder">

	<div id="wrapper">
		<div id="header">
			
			<?php include ROOT."themes/nav.inc.php"; ?>
		</div>
		<?php echo notification(); ?>
		<div id="clinic-finder" class="clear-block">
		<div class="links"></div>
			
			<form method="post" action="./index.php" accept-charset="UTF-8" method="post" id="clinic-finder-form" class="clear-block">
	  
				<div><div class="form-item" id="edit-gmap-address-wrapper">
				 <label for="edit-gmap-address"><?php echo $lang['PLEASE_ENTER_YOUR_LOCATION']; ?>: </label>
				 <input type="text" maxlength="128" name="address" id="address" size="60" value="" class="form-text" autocomplete="off" />
				</div>
				<?php 
				// support unicode
				mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");
				$cats = $db->get_rows("SELECT categories.* FROM categories WHERE categories.id!='' ORDER BY categories.cat_name ASC");

				?>
				<div class="form-item" id="edit-products-wrapper">
				 <label for="edit-products"><?php echo $lang['SSF_CHOOSE_A_CATEGORY']; ?>: </label>
				 <select name="products" class="form-select" id="edit-products" multiple="multiple">
				 <?php if(!empty($cats)): ?>
					<?php foreach($cats as $k=>$v): ?>
					<option value="<?php echo $v['id']; ?>"><?php echo $v['cat_name']; ?></option>
					<?php endforeach; ?>
					<?php endif; ?>
				 </select>
				</div>
				
				
				<input type="submit" name="op" id="edit-submit" value="<?php echo $lang['FIND_STORE']; ?>" class="btn btn-large btn-primary" />
				<input type="hidden" name="form_build_id" id="form-0168068fce35cf80f346d6c1dbd7344e" value="form-0168068fce35cf80f346d6c1dbd7344e"  />
				<input type="hidden" name="form_id" id="edit-clinic-finder-form" value="clinic_finder_form"  />

				</div>
				<input type="hidden" id="distance" name="distance" value="200">
				<input type="hidden" name="catlist" id="" />
				</form>
						
					  <div id="map_canvas"><?php echo $lang['JAVASCRIPT_ENABLED']; ?></div>
					  <div id="results">        
						<h2><?php echo $lang['STORES_NEAR_YOUR']; ?></h2>
						<p class="distance-units">
						  <label class="km <?php if(DEFAULT_DISTANCE=="mi") {?>unchecked<?php } ?>" units="km" <?php if(DEFAULT_DISTANCE=="mi") {?> checked="unchecked"<?php } ?> >
							<input type="radio" name="distance-units" value="kms" /><?php echo $lang['KM']; ?>
						  </label>
						  <label class="miles <?php if(DEFAULT_DISTANCE=="km") {?>unchecked<?php } ?>" <?php if(DEFAULT_DISTANCE=="km") {?> checked="unchecked"<?php } ?>units="miles">
							<input type="radio" name="distance-units" value="miles" /><?php echo $lang['MILES']; ?>
						  </label>
						</p>
						<ol style="height: 445px; display: block; " id="list"></ol>
					  </div>
					  
					  <div id="direction">
					  <form method="post" id="direction-form">
					  <p>
					  <table><tr>
					  <td><?php echo $lang['STORE_ORIGIN']; ?>:</td><td><input id="origin-direction" name="origin-direction" class="orides-txt" type=text /></td>
					  </tr>
					  <tr>
					  <td><?php echo $lang['STORE_DESTINATION']; ?>:</td><td><input id="dest-direction" name="dest-direction" class="orides-txt" type=text readonly /></td>
					  </tr>
					  </table>
					  <div id="get-dir-button" class="get-dir-button"><input type=submit id="get-direction" class="btn" value="<?php echo $lang['STORE_GET_DIRECTION']; ?>"><input type="button" style="display:none; margin-left:5px;" id="DirectionPrint" class="btn btnPrint" disabled="disabled" value="<?php echo $lang['STORE_PRINT']; ?>"/> <a href="javascript:directionBack()"><?php echo $lang['STORE_BACK_BTN']; ?></a></div></p>
					  </form>
					  </div>
					  
	</div>
    <div class="overlay" id="overlay-contact-clinic-form"><div class="form-wrapper"></div></div>
  </div>
   <center>

  <br>
  
  <h4><?php echo $lang['EMBED']; ?>:</h4>
  <textarea id="embed" style="width:650px;"><iframe src="<?php echo ROOT_URL; ?>embed.php" width="980px" height="630px"  scrolling=no frameborder=no></iframe></textarea>
  <br>

  </center>
  <!-- Contact us form data-model code --> 
  <div class="modal fade" id="myModal"  style="display:none;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" style="margin-top:10px;" aria-hidden="true">×</button>
                          <h4 class="modal-title" id="myModalLabel" align='center' style="margin-top:10px;"><?php echo $lang['CONTACT_THIS_STORE']; ?></h4>
                      </div> <!-- /.modal-header -->
                      <div class="modal-body">
                        <div class="col-lg-12" align='center'>
						<form id='ssf-contact-form' action='#' method='post' name='form' role='form'>
						<div><h4 id='ssf-msg-status'></h4></div>
						<div>
						<label>
						<span><?php echo $lang['ADMIN_NAME']; ?>: (<div class='ssf-red-star'>*</div>)</span>
						<input placeholder='Please enter your name' name='ssf_cont_name' type='text' tabindex='1' required autofocus>
						</label>
						</div>
						<div>
						<label>
						<span><?php echo $lang['EMAIL']; ?>: (<div class='ssf-red-star'>*</div>)</span>
						<input placeholder='Please enter your email address' name='ssf_cont_email' type='email' tabindex='2' required>
						</label>
						</div>
						<div>
						<label>
						<span><?php echo $lang['TELEPHONE']; ?></span>
						<input placeholder='Please enter your number' name='ssf_cont_phone' type='tel' tabindex='3'>
						</label>
						</div>
						<div>
						<label>
						<span><?php echo $lang['POPUP_MESSAGE']; ?>: (<div class='ssf-red-star'>*</div>)</span>
						<textarea placeholder='Include all the details you can' name='ssf_cont_msg' tabindex='4' required></textarea>
						</label>
						</div>
						<div>
						<button name='submit' type='button' onclick='ssfEmailSubmit();'><?php echo $lang['POPUP_SEND_BTN']; ?></button>
						</div>
                        </form>
                    </div>
                    </div> 
                  <div class="modal-footer">
                  <div id="loginMsg" style="color:#900; font-size:12px; font-weight:600; text-align:center;"></div>
                  <div id="signupMsg" style="color:#090; font-size:12px; font-weight:600; text-align:center;"></div>
                  </div> 
                  </div>
                  </div>
          </div>
<!-- Contact us form data-model code end here -->      
	
	<script>
		if(geo_settings==1){
				$('#address').val(geoip_city()+", "+geoip_country_name());
		} else {
				$('#address').val(default_location);
		}
			
			$(function() {
				$('#edit-products').multiselect({
				includeSelectAllOption: true
				});
			});
	 
	</script>
	
	
<?php include ROOT."themes/footer.inc.php"; ?>
</body>
</html>