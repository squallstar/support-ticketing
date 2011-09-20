<?php
/**
 * Support-Ticketing Login form
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
	$delegate->redirectToList();
}

if (isset($_POST['act'])) {

	$data = array(
		'username' => strtolower($_POST['username']),
		'password' => $_POST['password']
	);
	
	if ($delegate->tryLogin($data)) {
		if (strlen($_GET['redirect'])) {
			$delegate->redirect($_GET['redirect'], true);
		}else{
			$delegate->redirectToList();
		}
	}else{
		$error = true;
	}
}

$delegate->renderHeader();
?>


<form action="" method="post">
	<fieldset>
		<input type="hidden" name="act" value="1" />
		<label for="username">Nome utente</label><br />
		<input name="username" id="username" class="default" value="<?php echo $_POST['username']; ?>"/><br /><br />
		<label for="username">Password</label><br />
		<input type="password" name="password" class="default" id="password" value="" /><br /><br />
		<input type="submit" value="Entra" class="button blue"/>
		<?php if ($error) { ?>
		
		<span class="error">Nome utente o password non validi.</span>
		<?php } ?>
	</fieldset>
</form>

<script type="text/javascript">
$(document).ready(function() {
	$('#username').focus();
});
</script>

<?php $delegate->renderFooter(); ?>