<?php
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'email_invitation_api.php' );
?>
<br />
<div align="center">
<?php
	$t_invitation = invitation::create(FIRST_INTERVIEW_INVITE, "test name", 4065); 
	$t_invitation->send("emmanouil.maragkos@nsn.com");
	#$t_invitation->send("emmanouil.maragkos@nsn.com", "another subject");
?>
</div>
