<?php
/**
 * Support-Ticketing Projects list
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(true, $_POST['notifications']=='no'?false:true);

if (!$delegate->isAdmin()) {
	$delegate->redirect('list');
}

if (isset($_POST['projectname']) && strlen($_POST['projectname'])) {
	$delegate->addProject($_POST['projectname']);
	$delegate->clearProjectsCache();
}

if (isset($_GET['remove'])) {
	$id = $delegate->getProjectIdByHash($_GET['remove']);
	$delegate->deleteProject($id);
	$delegate->clearProjectsCache();
}


$delegate->renderHeader();
?>

<h1>GESTIONE PROGETTI</h1>

<a href="<?php echo $delegate->root; ?>list" class="button blue buttonmargin">Torna alla lista</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="#" onclick="$('#add_project').fadeIn();$(this).fadeOut();" class="button buttonmargin">Aggiungi nuovo progetto</a>
<div class="hidden" id="add_project">

	<form action="" method="post" class="replyform" enctype="multipart/form-data"><br /><br />
		<fieldset>
			<label for="projectname">Nome del progetto:</label>&nbsp;&nbsp;
			<input class="default" name="projectname" id="projectname"/>&nbsp;&nbsp;
			<input type="submit" class="button blue" value="Aggiungi progetto" />
		</fieldset>
	</form>

</div>
<br /><br /><br />

<?php foreach ($delegate->myProjects() as $id => $arr) {
$users = $delegate->getProjectUsers($id);
$i = false;
?>

<div class="ticket grey">
	<div class="left">
		<div class="secondline"><?php echo $arr['name']; ?></div>
		<span class="firstline"><strong>Utenti: </strong><?php foreach ($users as $u) { echo ($i?', ':'').$u['realname']; $i=true;  } ?><br />
		<a href="<?php echo $delegate->root; ?>graphs/project/<?php echo $arr['hash']; ?>">Visualizza Grafico</a></span> - 
		<a href="<?php echo $delegate->root; ?>manage/projects?remove=<?php echo $arr['hash']; ?>" onclick="return confirm('Sei sicuro di voler eliminare questo progetto?');">Elimina progetto</a>
	</div>
	<div class="right">
		<a href="<?php echo $delegate->root; ?>manage/project/<?php echo $arr['hash']; ?>" class="button grey">Associazioni</a>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>


<br /><br /><br />




<?php $delegate->renderFooter(); ?>