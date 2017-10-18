<?php

class ContactFormHandler {

	function handleContactForm() {

		if($this->isFormSubmitted() && $this->isNonceSet()) {
			if($this->isFormValid()) {
				$this->sendContactForm();
			} else {
				$this->displayContactForm();
			}
		} else {
			$this->displayContactForm();
		}

	   }

		 public function sendContactForm() {
		     	$contactName = $_POST['contactname'] ;
		     	$contactEmail = $_POST['contactemail'];
		     	$contactContent = $_POST['contactcontent'];

		     	$emailTo = get_option( 'admin_email');


		     	$subject = 'New contact from  From '.$contactName;
		 		$body = "Contact Name: $contactName \n\nContact Email: $contactEmail \n\nContact contents: $contactContent";
		 		$headers = 'From: '.$contactName.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $contactEmail;

		 		wp_mail($emailTo, $subject, $body, $headers);

		 		echo "Contact Us sent. We will reply to your query soon.";

		     }


	   function isNonceSet() {
	   	if( isset( $_POST['nonce_field_for_submit_contact_form'] )  &&
	   	  wp_verify_nonce( $_POST['nonce_field_for_submit_contact_form'], 'submit_contact_form' ) ) return true;
	   	else return false;
	   }

	   function isFormValid() {
	   	//Check all mandatory fields are present.
		if ( trim( $_POST['contactname'] ) === '' ) {
			$error = 'Please enter your name.';
			$hasError = true;
		} else if (!filter_var($_POST['contactemail'], FILTER_VALIDATE_EMAIL)  ) {
			$error = 'Please enter a valid email.';
			$hasError = true;
		} else if ( trim( $_POST['contactcontent'] ) === '' ) {
			$error = 'Please enter the content.';
			$hasError = true;
		}

		//Check if any error was detected in validation.
		if($hasError == true) {
			echo $error;
			return false;
		}
		return true;
	   }

		function isFormSubmitted() {
	   	if( isset( $_POST['submitContactForm'] ) ) return true;
	   	else return false;
	   }

	//This function displays the Contact form.
    public function displayContactForm() {
    	?>
    	<div id ="contactFormSection">
	    	<form action="" id="contactForm" method="POST" enctype="multipart/form-data">

			    <fieldset>
			        <label for="name">Your Name</label>

			        <input type="text" name="contactname" id="contactname" />
			    </fieldset>

			    <fieldset>
			        <label for="email">Your Email</label>

			        <input type="text" name="contactemail" id="contactemail" />
			    </fieldset>

			    <fieldset>
			        <label for="content">Contents</label>

			        <textarea name="contactcontent" id="contactcontent" rows="10" cols="35" ></textarea>
			    </fieldset>

			    <fieldset>
						
			        <button type="submit" name="submitContactForm" class="btn btn-primary">Send Query</button>
			    </fieldset>

			    <?php wp_nonce_field( 'submit_contact_form' , 'nonce_field_for_submit_contact_form'); ?>

			</form>
		</div>
		<?php
    }

}

?>
