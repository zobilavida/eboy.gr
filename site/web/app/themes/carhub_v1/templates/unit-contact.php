


<?php
   // generate a new token for the $_SESSION superglobal and put them in a hidden field
	$newToken = generateFormToken('form1');
?>

<body>

    <div id="page-wrap">

    <h1>Website Change Request Form</h1>

	<form action="https://carhub.gr/app/themes/carhub_v1/form.php" method="post" id="change-form">

	    <input type="hidden" name="token" value="<?php echo $newToken; ?>">

		<div class="rowElem">
            <label for="req-name">Your Name*:</label>
            <input type="text" id="req-name" name="req-name" class="required" minlength="2" value="<?php echo $_COOKIE["WRCF-Name"]; ?>" />
        </div>

        <div class="rowElem">
            <label for="req-email">Your Email:</label>
            <input type="text" name="req-email" class="required email" value="<?php echo $_COOKIE["WRCF-Email"]; ?>" />
        </div>

        <div class="rowElem">
		    <label>Type of Change:</label>

		    <div id="changeTypeArea">

    			<input type="radio" name="typeOfChange" id="existing" value="Change to Existing Content" checked="checked" />
    			<label for="existing">Change to Existing Content</label>

    			<div class="clear"></div>

    			<input type="radio" id="add-new" name="typeOfChange" value="Add New Content" />
    			<label for="add-new">Add New Content</label>

			</div>
        </div>

        <div class="rowElemSelect">
			<label for="urgency">How Urgent:</label>
			<select name="urgency">
				<option value="Super Wicked Urgent">Super Wicked Urgent</option>
				<option value="ASAP">ASAP</option>
				<option value="When you get to it">When you get to it</option>
				<option value="It can wait">It can wait</option>
			</select>
		</div>

        <div class="rowElem">
            <label for="URL-main">URL of Page:</label>
            <input type="text" name="URL-main" class="required url" />
        </div>

		<div class="rowElem">
		  <label for="mult">Change on multiple pages?</label>
		  <input type="checkbox" name="mult" id="multCheck" />
        </div>

        <div id="addURLSArea">
            <div class="rowElem">
    		  <label for="addURLs">Additional URL's / Areas:</label>
    		  <textarea cols="40" rows="4" name="addURLS"></textarea>
            </div>
        </div>

        <div id="curTextArea">
    		<div class="rowElem">
    		  <label for="curText">CURRENT Text / Content:</label>
    		  <textarea cols="40" rows="8" name="curText"></textarea>
            </div>
        </div>

		<div class="rowElem" id="newTextArea">
		  <label for="newText">NEW Text / Content:</label>
		  <textarea cols="40" rows="8" name="newText" class="required" minlength="2"></textarea>
        </div>

		<div class="rowElem">
		  <label> &nbsp; </label>
		  <input type="submit" value="Send Request!" />
        </div>

        <div class="rowElem">
		  <label> &nbsp; </label>
		  <input type="checkbox" name="save-stuff" />
		  <label for="save-stuff">&nbsp; Save Name and Email?</label>
        </div>

	</form>

	</div>
