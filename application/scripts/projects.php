<?php
/**
 * Support-Ticketing Projects list
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

//$delegate->clearProjectsCache();

$delegate->renderHeader();
?>

<h1>GESTIONE PROGETTI</h1>



<a href="<?php echo $delegate->root; ?>list" class="button blue buttonmargin">Torna alla lista</a>&nbsp;&nbsp;&nbsp;&nbsp;
<!--<a href="#" onclick="" class="button buttonmargin">Aggiungi nuovo progetto</a>-->
<br /><br /><br />

<?php foreach ($delegate->myProjects() as $id => $arr) {
$users = $delegate->getProjectUsers($id);
$i = false;
?>

<div class="ticket grey">
	<div class="left">
		<div class="secondline"><?php echo $arr['name']; ?></div>
		<span class="firstline"><strong>Utenti: </strong><?php foreach ($users as $u) { echo ($i?', ':'').$u['realname']; $i=true;  } ?></span>
	</div>
	<div class="right">
		<a href="<?php echo $delegate->root; ?>manage/project/<?php echo $arr['hash']; ?>" class="button grey">Associazioni</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>


<br /><br /><br />




<?php $delegate->renderFooter(); ?>