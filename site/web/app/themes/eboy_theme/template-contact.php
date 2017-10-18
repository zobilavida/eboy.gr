<?php
/**
 * Template Name: Contact Template
 */
?>

<div class="container">
  <div class="row">
    <div class="col-12 custom-form">
    <?php
    				$wpContactFormObj = new ContactFormHandler();
    				$wpContactFormObj->handleContactForm();
    			?>
          </div>
        </div>
</div>
