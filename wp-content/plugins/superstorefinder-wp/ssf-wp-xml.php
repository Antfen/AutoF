<?php
function xml_out($buff) {
	preg_match("@<locator>.*<\/locator>@s", $buff, $the_xml);
	return $the_xml[0];
}
if (empty($_GET['debug'])) {
	ob_start("xml_out");
}
header("Content-type: text/xml");
include("ssf-wp-inc/includes/ssf-wp-env.php");
global $ssf_wp_vars,$wpdb;

$ssf_wp_ap_xml = array("ssf_wp_custom_fields", "ssf_wp_xml_columns");
foreach ($ssf_wp_ap_xml as $value){ if (!empty($_GET[$value])){ unset($_GET[$value]); } }

$ssf_wp_custom_fields = (!empty($ssf_wp_xml_columns))? ", ".implode(", ", $ssf_wp_xml_columns) : "" ;

if (!empty($_GET)) { $_sl = $_GET; unset($_GET['mode']); unset($_GET['lat']); unset($_GET["lng"]); unset($_GET["radius"]); unset($_GET["edit"]);}
$_GET=array_filter($_GET);

$ssf_wp_param_where_clause="";
if (function_exists("do_ssf_wp_hook")){ do_ssf_wp_hook("ssf_wp_xml_query"); }
$query=$wpdb->get_results("SELECT ssf_wp_id,ssf_wp_address, ssf_wp_address2, ssf_wp_store, ssf_wp_city, ssf_wp_state, ssf_wp_zip, ssf_wp_latitude, ssf_wp_longitude, ssf_wp_description, ssf_wp_url, ssf_wp_ext_url, ssf_wp_embed_video,ssf_wp_contact_email, ssf_wp_default_media, ssf_wp_hours, ssf_wp_phone, ssf_wp_fax, ssf_wp_email, ssf_wp_image, ssf_wp_tags FROM ".SSF_WP_TABLE." WHERE ssf_wp_store<>'' AND ssf_wp_longitude<>'' AND ssf_wp_longitude!='0' AND ssf_wp_latitude<>'' ORDER BY ssf_wp_store ASC", ARRAY_A);
$query2=$wpdb->get_results("SELECT *  FROM ".SSF_WP_TAG_TABLE." WHERE ssf_wp_tag_id!=0 GROUP BY(ssf_wp_tag_slug)", ARRAY_A);
	
echo "<locator>\n";
// Xml header
function tagsWithNumber($tag) {
    $checkNumber=preg_match('/^\d/', $tag) === 1;
	if($checkNumber){
			$tag='_'.$tag;
	}
	$tag= str_replace(" / ","or",$tag);
	$tag= str_replace("/","or2",$tag);
	$tag=str_replace(" &amp; ","_n_",$tag);
	$tag=str_replace("&amp;","_n2_",$tag);
	$tag= str_replace(" ","",$tag);
	//$tag= str_replace('�',"_s_",$tag);
	$tag= str_replace("\\","or",$tag);	
	$tag=str_replace(",","",$tag);
	$tag=str_replace('&amp;#39;','',$tag);
	$tag=str_replace("&#39;","",$tag); 
	$tag=str_replace(":","_",$tag); 	
	$tag= str_replace("-","_d_",$tag);
    $tag= str_replace("(","_b_o_",$tag);	
    $tag= str_replace(")","_b_c_",$tag);
    $tag= str_replace(".","_dot_",$tag);
    $tag= str_replace("~","_t_",$tag);		
	$tags=htmlentities($tag, ENT_QUOTES, 'UTF-8');
	if (strpos($tags, '&rsquo;') !== false) {
		$tag= str_replace("&rsquo;","_s_",$tags);
    }	
	return $tag;
}
foreach ($query2 as $row2) {
   $tag=(trim($row2['ssf_wp_tag_slug'])!="")? ssfParseToXML($row2['ssf_wp_tag_slug']) : " " ;
   $tagid=(trim($row2['ssf_wp_tag_id'])!="")? ssfParseToXML($row2['ssf_wp_tag_id']) : " " ;
// tag with space
$copy= $tag;
$tag=tagsWithNumber($tag);

echo "\n
<legend>\n
<label>\n
<tag>".$tag."</tag>\n
<copy>".ssfParseToXML(__($copy,SSF_WP_TEXT_DOMAIN))."</copy>\n
</label>\n
</legend>\n";

}

// Xml header
echo "<store>\n";

$sortOrd=0;

$checkAddon=false;
$addonRating=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-rating-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
if(!empty($addonRating) && is_dir(SSF_WP_ADDONS_PATH.'/ssf-rating-addon-wp'))
{	
	$checkAddon=true;
}

foreach ($query as $row) {
   $sortOrd=$sortOrd+1;
   $addr2=(trim($row['ssf_wp_address2'])!="")? " ".ssfParseToXML($row['ssf_wp_address2']). ", " : " " ;
 	$city=(trim($row['ssf_wp_city'])!="")? " ".ssfParseToXML($row['ssf_wp_city']). ", " : " " ;

  if(!empty($row['ssf_wp_contact_email']) && $row['ssf_wp_contact_email']!='0')
  {
  	$contactEmail=(trim($row['ssf_wp_contact_email'])=="1")? ssfParseToXML($row['ssf_wp_email']) : ssfParseToXML($ssf_wp_vars['ssf_conatct_email']) ;
  }
  else {
	  $contactEmail='';
  }
  $row['ssf_wp_description']=(trim($row['ssf_wp_description'])=="&lt;br&gt;" || trim($row['ssf_wp_description'])=="")? "" : $row['ssf_wp_description'];
  $row['ssf_wp_hours']=(trim($row['ssf_wp_hours'])=="&lt;br&gt;" || trim($row['ssf_wp_hours'])=="")? "" : $row['ssf_wp_hours'];
  $row['ssf_wp_url']=(!ssf_url_test($row['ssf_wp_url']) && trim($row['ssf_wp_url'])!="")? "http://".$row['ssf_wp_url'] : $row['ssf_wp_url'] ;
  $row['ssf_wp_ext_url']=(!ssf_url_test($row['ssf_wp_ext_url']) && trim($row['ssf_wp_ext_url'])!="")? "http://".$row['ssf_wp_ext_url'] : $row['ssf_wp_ext_url'] ;
  // Xml nodes 
  echo '<item>';
  echo '<location>' . ssfParseToXML(__($row['ssf_wp_store'],SSF_WP_TEXT_DOMAIN)) . '</location>';
  echo '<address>' .ssfParseToXML(__($row['ssf_wp_address'],SSF_WP_TEXT_DOMAIN)) .$addr2.ssfParseToXML(__($city,SSF_WP_TEXT_DOMAIN)). ' ' .ssfParseToXML(__($row['ssf_wp_state'],SSF_WP_TEXT_DOMAIN)).' ' .ssfParseToXML($row['ssf_wp_zip']).'</address>';
  echo '<sortord>'.$sortOrd.'</sortord>';
  echo '<latitude>' . $row['ssf_wp_latitude'] . '</latitude>';
  echo '<longitude>' . $row['ssf_wp_longitude'] . '</longitude>';
  echo '<description>' .ssfParseToHXML(__($row['ssf_wp_description'],SSF_WP_TEXT_DOMAIN)). '</description>';
  echo '<website>' . ssfParseToXML($row['ssf_wp_url']) . '</website>';
  echo '<exturl>' . ssfParseToXML($row['ssf_wp_ext_url']) . '</exturl>';
  echo '<operatingHours>' .ssfParseToHXML(__($row['ssf_wp_hours'],SSF_WP_TEXT_DOMAIN)). '</operatingHours>';
  echo '<embedvideo>'.base64_encode(htmlspecialchars_decode($row['ssf_wp_embed_video'])).'</embedvideo>';
  echo '<defaultmedia>'.$row['ssf_wp_default_media'].'</defaultmedia>';
  echo '<telephone>' . ssfParseToXML($row['ssf_wp_phone']) . '</telephone>';
  echo '<fax>' . ssfParseToXML($row['ssf_wp_fax']) . '</fax>';
  echo '<email>' . ssfParseToXML($row['ssf_wp_email']) . '</email>';
  echo '<contactus>'.$contactEmail.'</contactus>';
  echo '<storeId>'.$row['ssf_wp_id'].'</storeId>';
  
  if($ssf_wp_vars['zip_label_show']=='true'){
     echo '<zip>'.$row['ssf_wp_zip'].'</zip>';
  }
  if($ssf_wp_vars['zip_label_show']=='true'){
     echo '<state>'.ssfParseToXML(__($row['ssf_wp_state'],SSF_WP_TEXT_DOMAIN)).'</state>';
  }

 if($checkAddon==true){ 
  $rating=$wpdb->get_results("SELECT count(*) as count, AVG(ssf_wp_ratings_score) as score FROM ".SSF_WP_SOCIAL_TABLE." WHERE 1 AND ssf_wp_store_id = '".$row['ssf_wp_id']."'", ARRAY_A);
	 echo '<storeRat>'.round($rating[0]["score"], 2).'</storeRat>';
	 echo '<storeTotalRat>'.$rating[0]["count"].'</storeTotalRat>';
  }
  
  // store img
  $ssf_wp_uploads=wp_upload_dir();
  
  if ( is_ssl() ) {
	$ssf_wp_uploads = str_replace( 'http://', 'https://', $ssf_wp_uploads );
  }

  $ssf_wp_uploads_path=$ssf_wp_uploads['basedir']."/ssf-wp-uploads"; 
			$upload_dir=$ssf_wp_uploads_path."/images/".$row['ssf_wp_id'].'/*';
			$upload_dir_img=$ssf_wp_uploads_path."/images/".$row['ssf_wp_id'];
			$ssf_wp_uploads_base=$ssf_wp_uploads['baseurl']."/ssf-wp-uploads";
			
			$img = '';
			$files = array();
			if(is_dir($upload_dir_img))
			{
			foreach (glob($upload_dir) as $file) {
			  $files[] = $file;
			}
		
			if($files !== FALSE && isset($files[0])) {
			$files[0] = str_replace('ori_', '', $files[0]);
			$files[0] = str_replace($ssf_wp_uploads_path."/images/".$row['ssf_wp_id'], '', $files[0]);
			
				$img = $ssf_wp_uploads_base."/images/".$row['ssf_wp_id'].$files[0];
			}
			}
 		    echo '<storeimage>'.ssfParseToXML($img).'</storeimage>';
   		   //*custom marker icon */
		    $upload_dir=$ssf_wp_uploads_path."/images/icons/".$row['ssf_wp_id'].'/*';
			$upload_dir_icon=$ssf_wp_uploads_path."/images/icons/".$row['ssf_wp_id'];
			$ssf_wp_uploads_base=$ssf_wp_uploads['baseurl']."/ssf-wp-uploads";
			
			$mrkr = '';
			$files = array();
			
			if(is_dir($upload_dir_icon))
			{
			foreach (glob($upload_dir) as $file) {
			  $files[] = $file;
			}
				if($files !== FALSE && isset($files[0])) {
				$files[0] = str_replace($ssf_wp_uploads_path."/images/icons/".$row['ssf_wp_id'], '', $files[0]);
				
					$mrkr = $ssf_wp_uploads_base."/images/icons/".$row['ssf_wp_id'].$files[0];
				}
			}
 		  echo '<custmmarker>'.ssfParseToXML($mrkr).'</custmmarker>';
  /* custom marker end  */  
		
  $row['ssf_wp_tags'] = str_replace('&#44;', ',', $row['ssf_wp_tags']);
  $tagarray = explode(',',trim($row['ssf_wp_tags']));
for($i=0;$i<sizeof($tagarray)-1;$i++){
	 $tags=tagsWithNumber($tagarray[$i]);
	 echo '<'.$tags.'>true</'.$tags.'>';
}
  if (!empty($ssf_wp_xml_columns)){ 
  $alrdy_used=array('name', 'address', 'street', 'street2', 'city', 'state', 'zip', 'lat', 'lng', 'distance', 'description', 'url', 'hours', 'phone', 'fax', 'email', 'image', 'tags');
  	foreach($ssf_wp_xml_columns as $key=>$value) {
  		if (!in_array($value, $alrdy_used)) { //can't have duplicate property names in xml
	  		$row[$value]=(!isset($row[$value]))? "" : $row[$value] ;
  			 echo "$value=\"" . ssfParseToXML($row[$value]) . "\" ";
  			 $alrdy_used[]=$value;
  		}
  	}
  }
  echo "</item>\n";
}

// End XML file
echo "</store>\n
</locator>\n";
if (empty($_GET['debug'])) {
	ob_end_flush();
}


?>