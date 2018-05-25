<?php
/**
 * Template Name: Post Insert
 */
?>

<form method="post" id="post_insert" name="front_end" enctype="multipart/form-data" action="<?php echo 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>" >
<input type="text" id="post_title"  placeholder="My Post Title" />
<input type="text" id="post_content" placeholder="My Post Content" />
<!--<input type="text" name="custom_1" value="Custom Field 1 Content" />
<input type="text" name="custom_2" value="Custom Field 2 Content" />-->
<button type="button" name="submit" id="submit" >Submit</button>
<input type="hidden" name="action" value="post" />
</form>
