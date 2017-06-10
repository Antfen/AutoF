	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>css/superstorefinder.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>css/bootstrap.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>css/bootstrap-responsive.css" media="all" /> 
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>css/bootstrap-multiselect.css" media="all" />
	
	<script src="<?php echo ROOT_URL; ?>/includes/geoip.php" type="text/javascript"><!--mce:1--></script>
	<?php if(MEGA_GOOGLE_API!=''){
	$google_api_key='key='.MEGA_GOOGLE_API.'&';	
	}else{
	$google_api_key='';	
	} ?>
	<script src="https://maps.googleapis.com/maps/api/js?<?php echo $google_api_key; ?>libraries=geometry,places"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/bootstrap.js"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/bootstrap-multiselect.js"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/super-store-finder.js" charset="utf-8"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/jquery.sort.js" charset="utf-8"></script>
	<script type="text/javascript" src="<?php echo ROOT_URL; ?>js/jquery.geocomplete.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT_URL; ?>css/typography.css" media="all" />
	
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />