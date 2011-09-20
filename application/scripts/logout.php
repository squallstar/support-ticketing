<?php
/**
 * Support-Ticketing logout form
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(false);

if ($delegate->isLoggedIn()) {
	$delegate->doLogout();
}
?>

Reindirizzamento in corso...
<script type="text/javascript">
<!--
function doRedirect() { 
location.href = "<?php echo $delegate->root; ?>login";
}
window.setTimeout("doRedirect()", 1500); 
//-->
</script>