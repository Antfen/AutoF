<?php
// include config file
include_once './includes/config.inc.php';
@session_start();
?>
<!DOCTYPE html>
<!-- saved from url=(0023)about:internet -->
<!--[if lt IE 7]> 
<html class="no-js ie lt9 lt8 ie6">
   <![endif]--> <!--[if IE 7]> 
   <html class="no-js ie lt9 lt8 ie7">
      <![endif]--> <!--[if IE 8]> 
      <html class="no-js ie lt9 ie8">
         <![endif]--> <!--[if IE 9]> 
         <html class="no-js ie ie9">
            <![endif]--> <!--[if (gt IE 9)|!(IE)]><!--> 
            <html class="no-js">
               <!--<![endif]--> 
               <head>
                  <meta charset="utf-8" />
                  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
                  <meta name="format-detection" content="telephone=no">
                  <!-- Disable iOS auto telephone number detection -->  
                  <link rel="icon" href="img/favicon.ico" type="image/x-icon" />

                  <title>Super Store Finder - Mega Theme</title>
				  <meta name="author" content="Joe Iz">
                  <meta name="keywords" content="mega locator theme, super store finder, street view, google direction, ajax, bootstrap, embed, geo ip, geolocation, gmap, google maps, jquery, json, map, responsive, store admin, store finder, store locator" />
				  <meta name="description" content="Super Store Finder &amp;#8211; Easy to use Google Maps API Store Finder Super Store Finder is a multi-language fully featured PHP Application integrated with Google Maps API v3 that allows customers to..." />

                  <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css'>
                  <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
				  <link rel="stylesheet" href="css/font-awesome.css">
                  <link href="css/normalize.css" rel="stylesheet" type="text/css" />
		<?php include ROOT."settings.php"; ?>
                  <link href="css/mega-superstorefinder.css" rel="stylesheet" type="text/css" />
                  
                  <script src="js/vendors/modernizr.min.js"></script> <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> 
				  <script>window.jQuery || document.write('<script src="js/vendors/jquery.1.11.0.min.js"><\/script>')</script>  
				  <!-- [if IE]> <script src="js/polyfills/html5shiv.3.7.0.min.js"></script> <![endif]--> <!--[if lt IE 9]> 
                  <link href="css/ie8.css" rel="stylesheet" type="text/css" />
                  <![endif]--> 
				  <style>
				  .icon.icon--checkbox, .icon.icon--success-tick {
				    border-radius:0 !important;
				  }
				  </style>
               </head>

               <body>
                 
                  <div class="main-site" id="mainSite">
                     
                     <div class="main-content" role="main" id="mainContent">
                        <div class="content-section">
                           <div class="section pad-bottom-2x medium-pad-bottom-4x store-locator-section">
                              <div class="store-locator-map-holder">
                                 <div class="store-locator-map" id="storeLocatorMap"> &nbsp;</div>
                              </div>
                              <div class="row large-pad-top-4x store-locator-top-half" id="storeLocator__topHalf">
                                 <div class="large-3 column">
                                    <div class="filter filter-popup filter-popup--medium-down panel shadowed">
                                       <div class="filter__row hide-for-medium-down">
                                          <div class="filter__items-counter"> <span id="storeLocator__currentStoreCount">0</span> <?php echo $lang['SSF_OF']; ?> <span id="storeLocator__totalStoreCount">0</span> <?php echo $lang['SSF_OUTLETS']; ?><br /> <a href="#show-all" id="filterShowAll"><?php echo $lang['SSF_SHOW_ALL']; ?></a></div>
                                          <a class="filter__options-clearer" href="#clear-all" id="filterOptionsClearer"><?php echo $lang['SSF_CLEAR_ALL']; ?> </a>
                                       </div>
                                       <div class="filter__row filter__row--regions" id="filter__states">
                                          <div class="filter__toggler arrow-toggler"> <?php echo $lang['SSF_BY_REGION']; ?></div>
                                          <div class="filter__toggler-contents togglerify-slider">
										 
                                             <ul class="small-block-grid-2 space-top">
                                                
                                                <li> <label><input id="storesRegion" class="js-inputify" name="storesRegion" type="radio" value="New York" /> North America </label></li>
												<li> <label><input id="storesRegion" class="js-inputify" name="storesRegion" type="radio" value="London" />  Europe </label></li>
                                                <li> <label><input id="storesRegion" class="js-inputify" name="storesRegion" type="radio" value="Rio de Janeiro" /> South America </label></li>
                                                <li> <label><input id="storesRegion" class="js-inputify" name="storesRegion" type="radio" value="Iran" /> Middle East </label></li>
												<li> <label><input id="storesRegion" class="js-inputify" name="storesRegion" type="radio" value="Cape Town" /> Africa </label></li>
                                                <li> <label><input id="storesRegion" class="js-inputify" name="storesRegion" type="radio" value="Seoul" /> Asia </label></li>
												
                                             </ul>
											  <!--<a href="#show-all" id="filterShowAllMobile" class="expand show-for-medium-down" data-close-popup="true"><br>Show All</a>-->
                                          </div>
                                       </div>
                                       
                                       <div class="filter__row filter__row--services" id="filter__services">
                                          <div class="filter__toggler arrow-toggler"> <?php echo $lang['SSF_CHOOSE_A_CATEGORY']; ?></div>
                                          <div class="filter__toggler-contents togglerify-slider" id="productsServicesFilterOptions">
                                             <div class="label__input-icon"> &nbsp;</div>
                                             <div class="label__contents"> <label class="label--vertical-align" style="margin-left:4px;"><input id="storesProductsServices" name="storesProductsServices" type="checkbox" value="default" /> <?php echo $lang['SSF_ALL_CATEGORY']; ?> </label></div>
                                          </div>
                                       </div>
                                       <div class="filter__row filter__row--cta">
                                          <div class="row">
                                             <div class="small-6 column"> <a class="button grey expand show-for-medium-down" data-close-popup="true" href="#"><?php echo $lang['SSF_CANCEL']; ?></a></div>
                                             <div class="small-6 large-offset-6 column"> <a class="button expand" data-close-popup="true" href="#" id="applyFilterOptions"><?php echo $lang['SSF_SELECT']; ?></a></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="large-9 column">
                                    <div class="panel shadowed store-locator__actions-bar">
                                       <table>
                                          <tbody>
                                             <tr>
                                                <td>
                                                   <div class="field-holder expand"> <input class="icon icon--search icon--dark sprite-icons-2x field-holder__icon" type="submit" value="" /> <input class="field expand" id="storeLocator__searchBar" placeholder="Search for nearby stores" type="text" /></div>
                                                </td>
                                                <td class="store-locator__geolocator-cell">
                                                   <div class="store-locator__geolocator" id="geolocator"> <span class="fa fa-crosshairs">&nbsp;</span></div>
                                                </td>
                                                <td class="hide-for-large-up store-locator__filter-toggler-cell" id="storeLocatorFilterToggler">
                                                   <div class="store-locator__filter-toggler"> <?php echo $lang['SSF_FILTERS']; ?></div>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                       <div class="store-locator__map-status" id="storeLocator__mapStatus">
                                          <div class="store-locator__map-status__spinner"> &nbsp;</div>
                                          <div class="store-locator__map-status__inner" id="storeLocator__mapStatus__inner"> &nbsp;</div>
                                          <div class="store-locator__map-status__closer" id="storeLocator__mapStatus__closer"> &nbsp;</div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="store-locator-bottom-half" id="storeLocator__bottomHalf">
                                 <div class="store-locator__infobox store-locator__infobox--main store-locator__infobox--mobile" id="mobileStoreLocatorInfobox">
								 <div id='info-img' class='info-img' style='position: relative;cursor: pointer;background-size: cover;background-image:url('')' data-plugins='open-modal' data-template='modal-photo-viewer'></div>
                                    <div class="infobox__closer"> &nbsp;</div>
                                    <div class="infobox__body">
                                       <div class="infobox__row infobox__title store-location"> Placeholder store name.</div>
                                       <div class="infobox__row store-address"> Placeholder address.</div>
                                        <?php if(SHOW_MOBILE_FIELD=='false'){ ?>
										   <div class="infobox__row store-website">  Website placeholder  </div>
											<div class="infobox__row store-email"> Email placeholder</div>
											<div class="infobox__row store-tel">Telephone placeholder </div> 
											<div class="infobox__row store-description">Description placeholder  </div>
										   <div class="info__head" id="info-operating-hours">
											<div id="info-operating" class="info__toggler actives" style="cursor:pointer;"></div>   Opening Hour </div>      
												<div class="info__toggler-contents store-operating-hours togglerify-slider" style="">
											</div>
											
										<?php } ?>
										

                                       <div class="infobox__row store-products-services"> &nbsp;</div>
                                    </div>
                                    <a class="infobox__row infobox__cta" href="#directions"><?php echo $lang['SSF_GET_DIRECTION']; ?> </a>
				    <a class="infobox__row infobox__stv" href="#streetview"><?php echo $lang['SSF_STREET_VIEW']; ?></a>
                                 </div>
                                 <div class="row panel pad medium-pad-2x" id="storeLocator__storeListRow">
                                    <div class="column hide-for-small-down">
                                       <h2 class="title space-bottom-3x"> <?php echo $lang['STORES_NEAR_YOUR']; ?></h2>
                                    </div>
                                    <span class="store-locator__store-list" id="storeLocator__storeList">&nbsp; </span>
                                 </div>
								 <input type='hidden' id='current_page' />
								<input type='hidden' id='show_per_page' />
								  <div id='page_navigation' style='margin-top:15px; text-align:center;'></div>
                              </div>
                              <div class="store-locator__infobox store-locator__infobox--main store-locator__infobox--in-map" id="storeLocatorInfobox">
                                 <div id='info-img' class='info-img' style='position: relative;cursor: pointer;background-size: cover;background-image:url('')' data-plugins='open-modal' data-template='modal-photo-viewer'></div>
								 <div class="infobox__inner">
								 
                                    <div class="infobox__closer"> &nbsp;</div>
                                    <div class="infobox__body">
                                       <div class="infobox__row infobox__title store-location"> Placeholder store name</div>
                                       <div class="infobox__row store-address"> Placeholder for address</div>

					 <div class="infobox__row">
					<div  id="info-website" class="infobox__subtitle" style="display:inline !important;"> <?php echo $lang['ADMIN_WEBSITE']; ?></div>
                                       <div class="store-website">
                                          <div class="infobox__subtitle"> <?php echo $lang['ADMIN_WEBSITE']; ?></div>
                                          Website placeholder
                                       </div>
				
                                       <div  id="info-email" class="infobox__subtitle" > <?php echo $lang['EMAIL']; ?></div>
							<div class="store-email">
                                          <div class="infobox__subtitle"> <?php echo $lang['EMAIL']; ?></div>
                                          Email placeholder
                                       </div>
                                       
					<div  id="info-tel" class="infobox__subtitle" style="display:inline !important;"> <?php echo $lang['ADMIN_TELEPHONE']; ?></div>
					<div class="store-tel">
                                          <div class="infobox__subtitle"> <?php echo $lang['ADMIN_TELEPHONE']; ?></div>
                                          Telephone placeholder
                                       </div>
 					<div id="info-description" class="infobox__subtitle" style="display:inline !important;"> <?php echo $lang['ADMIN_DESCRIPTION']; ?></div>
					<div class="store-description">
                                         
                                          Description placeholder
                                       </div>
					
					
				
					 <div class="info__head" id="info-operating-hours"><div id="info-operating" class="info__toggler actives" style="cursor:pointer;"></div>   Opening Hour </div>      
                        <div class="info__toggler-contents store-operating-hours togglerify-slider" style="">
						</div>
				
					
						<div class='store-contact-us' id='store-contact-us'>
							<a onclick='showConatctPopup();'  data-plugins='open-modal' data-template='modal-photo-viewer'>
							<div class='btn-super-info'> <?php echo $lang['CONTACT_THIS_STORE']; ?></div>
						</a></div>	
	<div style='clear:both;'>&nbsp;</div>
					</div>
					
                                     
                                    <a class="infobox__row infobox__cta" href="#directions"><?php echo $lang['SSF_GET_DIRECTION']; ?> </a>
				    <a class="infobox__row infobox__stv" href="#streetview"><?php echo $lang['SSF_STREET_VIEW']; ?></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     
                  </div>
				  
				  
				  
				   <!--[if lt IE 9]> 
                  <div class="main-popup-holder is-shown" id="mainPopupHolder">
                     <div class="popup is-shown" id="modernBrowserPopup">
                        <a href="javascript:hideOldBrowserPopup();" class="popup-closer">Close</a> 
                        <h1 class="popup-title">Browser not supported.</h1>
                        <div class="pad-horizontal-2x">
                           <p>Notice: Your current web browser will prevent you from viewing our store finder.</p>
                           <p><b>Try one of these browsers below:</b></p>
                           <div class="text-center pad-top" style="overflow: hidden;"> <a href="http://www.google.com/chrome" style="width: 33%; float: left; display: block;"> <img src="images/icons/browser-chrome.png" alt="Google Chrome" /> <br /> Google Chrome </a> <a href="http://windows.microsoft.com/ie" style="width: 33%; float: left; display: block;"> <img src="images/icons/browser-ie.png" alt="Internet Explorer" /> <br /> Internet Explorer 9 </a> <a href="http://www.firefox.com/" style="width: 33%; float: left; display: block;"> <img src="images/icons/browser-firefox.png" alt="Mozilla Firefox" /> <br /> Mozilla Firefox </a> </div>
                        </div>
                        <script> function hideOldBrowserPopup() { $('#mainPopupHolder, #modernBrowserPopup').removeClass('is-shown'); } </script> 
                     </div>
                  </div>
                  <![endif]--> 
				  
				  <div class='main-popup-holder' id='mainPopupHolder'>
                     <div class='popup' id='modernBrowserPopup'>
                        <a href='javascript:hidePopup();' class='popup-closer'>Close</a> 
                        <h1 class='popup-title'>Title</h1>
                        <div class='pad-horizontal-2x' class='popup-img'>
						<center>
						<img id='popup-image' src='' style="max-width:650px !important;"/>
						</center>
						</div>
                        <script> function hidePopup() { jQuery('#mainPopupHolder, #modernBrowserPopup').removeClass('is-shown'); } 
						function showPopup(t,i) { 
						
						jQuery('.popup-title').html(t);
						jQuery('#popup-image').attr('src',i);
						jQuery('#mainPopupHolder, #modernBrowserPopup').addClass('is-shown'); } 
						
						</script> 
                     </div>
                  </div>
                  
                  <div class='main-popup-holder' id='mainPopupContat'>
                     <div class='popup' style='max-width:700px; margin:25px 0 240px;' id='modernBrowserConatct'>
                        <a href='javascript:hideConatctPopup();' class='popup-closer ssflinks'>Close</a> 
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
						<button name='submit' type='button' id='contact-submit'><?php echo $lang['POPUP_SEND_BTN']; ?></button>
						</div>
                        </form>
                        <script> function hideConatctPopup() { jQuery('#mainPopupContat, #modernBrowserConatct').removeClass('is-shown'); } 
						function showConatctPopup() { 
						jQuery('#mainPopupContat, #modernBrowserConatct').addClass('is-shown'); } 
						</script> 
                     </div>
                  </div>
				
                   <script src="js/plugins/fastclick.min.js"></script> 
				   <script src="js/plugins/homebrew.js"></script> 
				   <script src="js/init.js"></script> 
				   <script> var fileUrl = ''; </script> 
				   <script src="js/mega-superstorefinder.js"></script> 
				  
               </body>
            </html>