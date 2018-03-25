<?php
include("ssf-wp-inc/includes/ssf-wp-env.php");
global $ssf_wp_vars,$wpdb;

if(isset($_POST['wpml_lang']) && !empty($_POST['wpml_lang'])){
	do_action( 'wpml_switch_language', $_POST['wpml_lang']);
}

$ratting_label=(trim($ssf_wp_vars['ratting_label'])!="")? ssfParseToXML($ssf_wp_vars['ratting_label']) : "Rating";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ratting_label, $ratting_label );
$ratting_label = apply_filters( 'wpml_translate_single_string', $ratting_label, 'superstorefinder-wp', $ratting_label);

$review_label=(trim($ssf_wp_vars['review_label'])!="")? ssfParseToXML($ssf_wp_vars['review_label']) : "reviews";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $review_label, $review_label );
$review_label = apply_filters( 'wpml_translate_single_string', $review_label, 'superstorefinder-wp', $review_label);
 
$reviewed_by=(trim($ssf_wp_vars['reviewed_by'])!="")? ssfParseToXML($ssf_wp_vars['reviewed_by']) : "Reviewed by";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $reviewed_by, $reviewed_by );
$reviewed_by = apply_filters( 'wpml_translate_single_string', $reviewed_by, 'superstorefinder-wp', $reviewed_by);

$no_ratting_msg=(trim($ssf_wp_vars['no_ratting_msg'])!="")? ssfParseToXML($ssf_wp_vars['no_ratting_msg']) : "No ratings for this product";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $no_ratting_msg, $no_ratting_msg );
$no_ratting_msg = apply_filters( 'wpml_translate_single_string', $no_ratting_msg, 'superstorefinder-wp', $no_ratting_msg);

$ratting_submit_msg=(trim($ssf_wp_vars['ratting_submit_msg'])!="")? ssfParseToXML($ssf_wp_vars['ratting_submit_msg']) : "Your rating has been added successfully";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ratting_submit_msg, $ratting_submit_msg );
$ratting_submit_msg = apply_filters( 'wpml_translate_single_string', $ratting_submit_msg, 'superstorefinder-wp', $ratting_submit_msg);

$allready_voted_msg=(trim($ssf_wp_vars['allready_voted_msg'])!="")? ssfParseToXML($ssf_wp_vars['allready_voted_msg']) : "You have already voted";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $allready_voted_msg, $allready_voted_msg );
$allready_voted_msg = apply_filters( 'wpml_translate_single_string', $allready_voted_msg, 'superstorefinder-wp', $allready_voted_msg);

$reCAPTCHA_warning=(trim($ssf_wp_vars['reCAPTCHA_warning'])!="")? ssfParseToXML($ssf_wp_vars['reCAPTCHA_warning']) : "Please enter your reCAPTCHA";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $reCAPTCHA_warning, $reCAPTCHA_warning );
$reCAPTCHA_warning = apply_filters( 'wpml_translate_single_string', $reCAPTCHA_warning, 'superstorefinder-wp', $reCAPTCHA_warning);


/***.featch exist rating from DB .***/
if(isset($_POST['ssf_wp_id']) && $_POST['action']=='select'){
$Id=$_POST['ssf_wp_id'];
$query=$wpdb->get_results("SELECT *  FROM ".SSF_WP_SOCIAL_TABLE." WHERE ssf_wp_store_id=$Id", ARRAY_A);
if(!empty($query)){
$result='<ul id="listingReviews">';
foreach ($query as $row) {
 $ratingStar='';
 $ratingValue='';
 for($i=0; $i<5; $i++){
    if($i<$row["ssf_wp_ratings_score"]){
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-on.png">';
	
	}else{
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-off.png">';
	}
 
 }
$yrdata= strtotime($row['ssf_comment_time']);
$yrdata= date('d-M-Y', $yrdata);
$result.='<li><div class="reviewmetadata">
<div class="reciewhead"><strong>'.$ratting_label.': '.$row["ssf_wp_ratings_score"].'</strong></div>
<div class="reciewStar">'
.$ratingStar.
'</div>
<br>
<span>
'.$reviewed_by.'
<strong> '.$row["ssf_wp_user_name"].'</strong>
on
'.$yrdata.'
</span>
</div>
<div class="reviewBody">
<p>'.$row["ssf_wp_comment"].'</p>
</div></li>';
}
$result.='</ul>';
}else{
$result=$no_ratting_msg;
}

$ssfResponse['ratingList'] = $result;
$query=$wpdb->get_results("SELECT count(*) as count, AVG(ssf_wp_ratings_score) as score FROM ".SSF_WP_SOCIAL_TABLE." WHERE 1 AND ssf_wp_store_id = $Id", ARRAY_A);
 $ratingStar='';
for($i=0; $i<5; $i++){
    if($i<round($query[0]["score"], 2)){
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-on.png">';
	}else{
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-off.png">';
	}

 }
 if(empty($query[0]["count"])){
 $query[0]["count"]=0;
 }
 $updated_rating='<div class="reciewInfoStar"><span class="ratingResultImg">'.$ratingStar.'</span><span class="totalReviewShow">('.$query[0]["count"].' '.$review_label.')</span></div>';
$ssfResponse['updated_rating'] = $updated_rating;

////////////
$rattingView='<div class="reciewInfoStar"><a class="infoRatingPopUp" onclick="showCommentPopup();">';
$rattingView .=$ratingStar;
$rattingView .='</a><span class="totalReviewShow">('.$query[0]["count"].' '.$review_label.')</span></div>';
$ssfResponse['updated_info'] = $rattingView;

//////////////

echo json_encode($ssfResponse);
}


/**.** Backend Comment and ratting List code here **.**/
/**.** ......................**.**/
/**.**/
if(isset($_POST['commentList']) && $_POST['action']=='select'){
	$ssfResponse['ratingList']='';
	$ssfResponse['updated_rating']='';
	
	if(!empty($_POST["page"])){
		$page_number = $_POST["page"]; }
	else{
		$page_number = 1;
	}
	
	if(!empty($_POST["search"])){
		$filter = $_POST["search"];
		$storefilter = " AND (lower(ssf_wp_store)  like  lower('%".$_POST["search"]."%') )";
		}
	else{
		$storefilter = '';
	}
	
	$qCount=$wpdb->get_results("SELECT count(*) as count FROM ".SSF_WP_TABLE." WHERE ssf_wp_id != 0 $storefilter", ARRAY_A);
		$item_per_page=5;
		$total_pages = ceil($qCount[0]["count"]/$item_per_page);
		$page_position = (($page_number-1) * $item_per_page);

$storeId=$wpdb->get_results("SELECT ssf_wp_id,ssf_wp_store FROM ".SSF_WP_TABLE." WHERE ssf_wp_store<>'' AND ssf_wp_longitude<>'' AND ssf_wp_latitude<>'' $storefilter ORDER BY ssf_wp_id ASC LIMIT $page_position, $item_per_page", ARRAY_A);
if(!empty($storeId)){
foreach($storeId as $rowid)
{
$Id=$rowid['ssf_wp_id'];
$query=$wpdb->get_results("SELECT count(*) as count, AVG(ssf_wp_ratings_score) as score FROM ".SSF_WP_SOCIAL_TABLE." WHERE 1 AND ssf_wp_store_id = $Id", ARRAY_A);
	$ratingStar='';
	for($i=0; $i<5; $i++){
		if($i<round($query[0]["score"], 2)){
		$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-on.png">';
		}else{
		$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-off.png">';
		}

	 }
 $updated_rating='<div class="reciewInfoStar" id="reciewInfoStar'.$Id.'"><span class="totalReviewShow">'
.$rowid['ssf_wp_store'].'</span>   '.$ratingStar.
'<span class="totalReviewShow" id="totalReviewShow'.$Id.'">('.$query[0]["count"].' '.$review_label.')</span></div>';

$result =  $updated_rating;
$query=$wpdb->get_results("SELECT *  FROM ".SSF_WP_SOCIAL_TABLE." WHERE ssf_wp_store_id=$Id", ARRAY_A);
if(!empty($query)){
$result .='<ul class="listingReviews">';
foreach ($query as $row) {
 $ratingStar='';
 for($i=0; $i<5; $i++){
    if($i<$row["ssf_wp_ratings_score"]){
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-on.png">';
	}else{
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-off.png">';
	}
 
 }
$yrdata= strtotime($row['ssf_comment_time']);
$yrdata= date('d-M-Y', $yrdata);
$result.='<li id="remove'.$row["ssf_wp_id"].'"><div class="reviewmetadata">
<div class="reciewhead"><strong>'.$ratting_label.': '.$row["ssf_wp_ratings_score"].'</strong></div>
<div class="reciewStar">'
.$ratingStar.'
<a id="'.$row["ssf_wp_id"].'" class="ssfRemoveReview" title="Delete">
<input type="hidden" value="'.$Id.'" class="getCommentId">
<i class="fa fa-times" aria-hidden="true"></i>
</a>
</div>
<br>
<span>
'.$reviewed_by.'
<strong> '.$row["ssf_wp_user_name"].'   ('.$row["ssf_wp_user_email"].') </strong> 
on
'.$yrdata.'
</span>
</div>
<div class="reviewBody">
<p>'.$row["ssf_wp_comment"].'</p>
</div></li>';
}
$result.='</ul>';
}else{
$result .='<ul class="listingReviews">
<li><div class="reviewBody">
<p>'.$no_ratting_msg.'</p></div></li></ul>';

//$result='';
}
$ssfResponse['ratingList'] .= $result;
}
$ssfResponse['pagination'] = paginate_function($item_per_page, $page_number, $qCount[0]["count"], $total_pages);
}

else{
$ssfResponse['ratingList']='<ul class="listingReviews">
<li><div class="reviewBody">
<p>'.$no_ratting_msg.'</p>
</div></li></ul>';
}
echo json_encode($ssfResponse);
}

/**.** End here backend Code **.**/


/***.*** Insert Query ***.***/
if(isset($_POST['pid']) && $_POST['action']=='insert'){

$recaptcha=$_POST['grecaptcharesponse'];
if(!empty($recaptcha))
{
$pid=intval($_POST["pid"]);
 $check=$wpdb->get_results("SELECT count(*) as count FROM ".SSF_WP_SOCIAL_TABLE." WHERE ssf_wp_user_email='".$_POST['ssf_wp_user_email']."' AND ssf_wp_store_id = '".$pid."'", ARRAY_A);
 if ($check[0]["count"] <= 0) {
	$_POST["ssf_wp_store_id"] = intval($_POST["pid"]);
	$_POST["ssf_wp_ratings_score"] = intval($_POST["score"]);
	$_POST["ssf_wp_comment"] = $_POST["comment"];
	$ssfResponse['error'] = FALSE;
	$ssfResponse['message'] = '';
	$ssfResponse['updated_rating'] = '';
	$return_message = "";
	$success = FALSE;

	$fieldList=""; $valueList="";
	foreach ($_POST as $key=>$value) {
		if (preg_match("@ssf_wp_@", $key)) {
		  
				$fieldList.="$key,";
				if (is_array($value)){
					$value=serialize($value); //for arrays being submitted
					$valueList.="'$value',";
				} else {
					$valueList.=$wpdb->prepare("%s", ssf_comma(stripslashes($value))).",";
				}
		}
	 }	 
	$fieldList=substr($fieldList, 0, strlen($fieldList)-1);
	$valueList=substr($valueList, 0, strlen($valueList)-1);
	$wpdb->query("INSERT INTO ".SSF_WP_SOCIAL_TABLE." ($fieldList) VALUES ($valueList)");
	$new_loc_id=$wpdb->insert_id;	
	if ($new_loc_id > 0) {
		$ssfResponse['message'] = $ratting_submit_msg;
	  } else {
		$ssfResponse['error'] = TRUE;
		$ssfResponse['message'] = "There was a problem updating your rating. Try again later";
	  }
	  
	if ($ssfResponse['error'] === FALSE) {
	  $query=$wpdb->get_results("SELECT count(*) as count, AVG(ssf_wp_ratings_score) as score FROM ".SSF_WP_SOCIAL_TABLE." WHERE 1 AND ssf_wp_store_id = '".$pid."'", ARRAY_A);
		if ($query[0]["count"] > 0) {
		  $ssfResponse['updated_rating'] = "Average rating <strong>" . round($query[0]["score"], 2) . "</strong> based on <strong>" . $query[0]["count"] . "</strong> users";
		} else {
		  $ssfResponse['updated_rating'] = '<strong>'.$ratting_label.': </strong>'.$no_ratting_msg;
		}
	 
	}
	}else{
		$ssfResponse['error'] =$allready_voted_msg;
	}
}
else
{
$ssfResponse['error'] =$reCAPTCHA_warning;
}

echo json_encode($ssfResponse);

}

if(isset($_POST['addon']) && $_POST['addon']=='remove'){
   $wpdb->query($wpdb->prepare("DELETE FROM ".SSF_WP_SOCIAL_TABLE." WHERE ssf_wp_id='%d'", $_POST['remove'])); 
   $id=$_POST['commentId'];
   $query=$wpdb->get_results("SELECT count(*) as count, AVG(ssf_wp_ratings_score) as score FROM ".SSF_WP_SOCIAL_TABLE." WHERE 1 AND ssf_wp_store_id =$id ", ARRAY_A);
 $ratingStar='';
for($i=0; $i<5; $i++){
    if($i<round($query[0]["score"], 2)){
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-on.png">';
	}else{
	$ratingStar.='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-off.png">';
	}

 }
	print $query[0]["count"];
}

/**get current ratting**/
if(isset($_POST['ssf_wp_id']) && $_POST['action']=='storeRating'){
$query=$wpdb->get_results("SELECT count(*) as count, AVG(ssf_wp_ratings_score) as score FROM ".SSF_WP_SOCIAL_TABLE." WHERE 1 AND ssf_wp_store_id = '".$_POST['ssf_wp_id']."'", ARRAY_A);
$rattingView='<div class="reciewInfoStar"><a class="infoRatingPopUp" onclick="showCommentPopup();">';
	for($i=0; $i<5; $i++){
		if($i<round($query[0]["score"], 2)){
		  $rattingView .='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-on.png">';
		}else{
		  $rattingView .='<img src="'.SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/img/star-off.png">';
		}	
	}
	$rattingView .='</a><span class="totalReviewShow">('.$query[0]["count"].' '.$review_label.')</span></div>';
	print $rattingView;
}
    
/**Pagination code here **/
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<ul class="pagination">';
        
        $right_links    = $current_page + 3; 
        $previous       = $current_page - 3; //previous link 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link
        
        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; //first link
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page'.$i.'">'.$i.'</a></li>';
                    }
                }   
            $first_link = false; //set first link to false
        }
        
        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
                $next_link = ($i > $total_pages)? $total_pages : $i;
                $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
                $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
        }
        
        $pagination .= '</ul>'; 
    }
    return $pagination; //return pagination links
}


//** Action to change view **//
if(isset($_POST['action']) && $_POST['action']=='status'){
$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_ADDON_TABLE." SET ssf_wp_addon_token='".$_POST['review_set']."' WHERE ssf_addon_name='%s'", 'Rating-Addon'));
echo "sucess";
}
?>