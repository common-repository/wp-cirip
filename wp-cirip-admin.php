<?php
define ( "cirip_pre", "cirip" );

$cirip_user = get_option ( cirip_pre . '_user' );
$cirip_pass = get_option ( cirip_pre . '_pass' );

if ($_POST [cirip_pre . '_hidden'] == 'Y')
{
	$new_cirip_user = $_POST [cirip_pre . '_user'];
	$new_cirip_pass = $_POST [cirip_pre . '_pass'];
	
	if ($cirip_user != $new_cirip_user)
	{
		update_option ( cirip_pre . '_user', $new_cirip_user );
		$cirip_user = $new_cirip_user;
	}
	
	if ($cirip_pass != $new_cirip_pass)
	{
		update_option ( cirip_pre . '_pass', $new_cirip_pass );
		$cirip_pass = $new_cirip_pass;
	}
	
	echo "<div class=\"updated\"><p><strong>";
	_e ( 'Cirip sucessfully updated .' );
	echo "</strong></p></div>";
} else
{

}

echo "<div class=\"wrap\">\n<h2>" . __ ( 'Cirip Settings', cirip_pre . '_trdom' ) . "</h2>\n<hr/>\n";
echo "<fieldset class=\"options\">\n";
echo "<form name=\"" . cirip_pre . "_form\" method=\"post\" action=\"" . str_replace ( '%7E', '~', $_SERVER ['REQUEST_URI'] ) . "\">\n";
echo "<input type=\"hidden\" name=\"" . cirip_pre . "_hidden\" value=\"Y\">\n";
echo "<label for=\"" . cirip_pre . "_user\">Cirip username:</label>\n";
echo "<input type=\"text\" name=\"" . cirip_pre . "_user\" value=\"" . get_option ( cirip_pre . '_user' ) . "\">\n";
echo "<br/>";
echo "<label for=\"" . cirip_pre . "_pass\">Cirip password:</label>\n";
echo "<input type=\"password\" name=\"" . cirip_pre . "_pass\" value=\"" . get_option ( cirip_pre . '_pass' ) . "\">\n";
echo "<br/>";
echo "<p class=\"submit\"><input type=\"submit\" value=\"Update settings\"></p>\n";
echo "</form>\n";
echo "</form>\n";
echo "</fieldset>\n";

echo "</div>\n\n";
?>

