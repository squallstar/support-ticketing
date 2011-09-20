<?php
/**
 * Support-Ticketing Project list view
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(true, $_POST['notifications']=='no'?false:true);

if (!$delegate->isAdmin()) {
	$delegate->redirect('list');
}


$id = $delegate->getProjectIdByHash($_GET['hash']);

if (!$id) {
	$delegate->redirect('manage/projects');
}

$users = $delegate->getProjectUsers($id, true);
$allusers = $delegate->getAllUsers();

if (isset($_POST['act'])) {
	$delegate->clearProjectAssociations($id);
	$_POST['user'][$_SESSION['me']['id']] = 1;
	foreach ($allusers as $user) {
		if (array_key_exists($user['id'], $_POST['user'])) {
			$delegate->addProjectAssociation($id, $user['id']);
		}
	}
	$users = $delegate->getProjectUsers($id, true);
}

$projects = $delegate->myProjects();
$project = $projects[$id];



$delegate->renderHeader();
?>

<h1><?php echo $project['name']; ?>: ASSOCIAZIONI</h1>

<a href="<?php echo $delegate->root; ?>manage/projects" class="button blue">Torna alla lista progetti</a>
<br /><br /><br />

<form action="" method="post" id="assoc" name="assoc">
	<input type="hidden" name="act" value="1" />

<?php foreach ($allusers as $user) { ?>
<div class="user_box">
	<input type="checkbox" value="1" name="user[<?php echo $user['id']; ?>]" <?php if (is_array($users) && array_key_exists($user['id'], $users)) { echo 'checked="checked"'; } if ($user['id'] == $_SESSION['me']['id']) { echo ' disabled'; } ?>/> <?php echo $user['realname']; ?>
</div>

<?php } ?>
<div class="clear"></div>
<br />
<a href="javascript:document.assoc.submit();" class="button">Salva associazioni</a>
</form>

<br />



<?php $delegate->renderFooter(); ?>