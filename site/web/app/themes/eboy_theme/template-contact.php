<?php
/**
 * Template Name: Contact Template
 */
?>

<div class="container">
  <div class="row">
<form action="<?php echo home_url(); ?>/mail.php" method="POST">
<p>Name</p> <input type="text" name="name">
<p>Email</p> <input type="text" name="email">
<p>Message</p><textarea name="message" rows="6" cols="25"></textarea><br />
<input type="submit" value="Send"><input type="reset" value="Clear">
</form>
</div>
</div>
