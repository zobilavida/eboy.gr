// JavaScript Document
/*Open Close hour and email script */
var $infoToggler = jQuery('.info__toggler'),
    $infoTogglerContents = jQuery('.info__toggler-contents');
    $infoToggler.togglerify({
        singleActive: true,
        slide: true,
        content: function(index) {
            return $infoTogglerContents.eq(index);
        }
    });

function SendMail(rcvEmail)
{
	var emailRegex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	var name = document.form.ssf_cont_name.value,
	email = document.form.ssf_cont_email.value,
	phone = document.form.ssf_cont_phone.value,
	message = document.form.ssf_cont_msg.value;
    email=email.trim();
	var storename =jQuery('#storeLocatorInfobox .store-location').html();
	
		if(name == "" )
		{
			document.form.ssf_cont_name.focus() ;
			document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>"+contact_plc_name+"</span>"
			return false;
		}
		if(email == "" )
		{
			document.form.ssf_cont_email.focus() ;
			document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>"+contact_plc_email+"</span>";
			return false;
		}
		else if(!emailRegex.test(email)){
		  document.form.ssf_cont_email.focus();
		  document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>"+contact_plc_email+" </span>";
		  return false;
		  }
		if(message == "" )
		{
			document.form.ssf_cont_msg.focus() ;
			document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>"+contact_plc_msg+"  </span>";
			return false;
		}
	   jQuery.ajax
		({
		type: "POST",
		url: ssf_wp_base + '/sendMail.php?t='+d.getTime(),
		data: {name: name, email: email, phone: phone, message:message, rcvEmail: rcvEmail,subject: storename},
		cache: false,
		success: function (html)
		{
			 document.getElementById("ssf-contact-form").reset();
			 document.getElementById("ssf-msg-status").innerHTML = "<span style='color:green;' id='imageMsgAlert'>"+ssf_msg_sucess+"</span>";
			 jQuery('#imageMsgAlert').fadeOut(5000);
		}
	});
}