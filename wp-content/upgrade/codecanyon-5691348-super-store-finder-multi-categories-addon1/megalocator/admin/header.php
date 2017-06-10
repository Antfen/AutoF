	<?php if(MEGA_GOOGLE_API_ADMIN!=''){
	$google_api_key='key='.MEGA_GOOGLE_API_ADMIN.'&';	
	}else{
	$google_api_key='';	
	} ?>
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>css/style.css" media="all" />
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" media="all" />
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-responsive.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>/css/bootstrap-multiselect.css" media="all" />
	<script src="<?php echo ROOT_URL; ?>../includes/geoip.php" type="text/javascript"><!--mce:1--></script>
	<script src="https://maps.googleapis.com/maps/api/js?<?php echo $google_api_key; ?>sensor=true&libraries=geometry,places"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/super-store-finder.js"></script>
	<script type="text/javascript" src="../js/bootstrap.js"></script>
	<script class="jsbin" type="text/javascript" src="<?php echo ROOT_URL; ?>/js/bootstrap-multiselect.js"></script>
