<?php
/**
 * Support-Ticketing List view
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(true);


if (strlen($_GET['filter']) && $_GET['filter'] != 'reset') {
	$filter = addslashes($_GET['filter']);
	$_SESSION['filter'] = $filter;
}else if ($_GET['filter'] == 'reset') {
	unset($_SESSION['filter']);
	$filter = null;
}

$delete = FALSE;
if (isset($_GET['remove_ticket'])) {
	if ($delegate->deleteTicket((int)$_GET['remove_ticket'])) {
		$delete=TRUE;
	}
}

$hide = FALSE;
if (isset($_GET['hide_ticket'])) {
	if ($delegate->hideTicket((int)$_GET['hide_ticket'])) {
		$hide=TRUE;
	}
}

$show = FALSE;
if (isset($_GET['show_ticket'])) {
	if ($delegate->hideTicket((int)$_GET['show_ticket'], FALSE)) {
		$show=TRUE;
	}
}


$delegate->renderHeader();
?>

<h1>LISTA SEGNALAZIONI</h1>

<select name="projects" class="projects" onchange="window.location.href = '<?php echo $delegate->root; ?>list/filter/'+$(this).val();">
	<option value="reset">(Tutti i progetti)</option>
<?php

foreach ($delegate->myProjects() as $id => $arr) { ?>
	<option value="<?php echo $arr['hash']; ?>"<?php echo $filter==$arr['hash']?' selected="selected"':''; ?>><?php echo $arr['name']; ?></option>
<?php } ?>
</select>

<a href="#" onclick="$('.projects').fadeIn('fast');$(this).fadeOut('fast');" class="button <?php if (!isset($filter)) echo 'blue'; ?> buttonmargin">Filtro progetti</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="<?php echo $delegate->root; ?>ticket/edit" class="button buttonmargin blue">Nuovo ticket</a>
<?php if ($delegate->isAdmin()) { ?>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $delegate->root; ?>manage/projects" class="button buttonmargin blue">Gestione progetti</a>
<?php } ?>

<br /><br />

<?php if ($delete) { ?>
<span class="error"><br />Il ticket &egrave; stato eliminato.<br /></span>
<?php } ?>

<?php if ($hide) { ?>
<span class="error"><br />Il ticket &egrave; stato nascosto.<br /></span>
<?php } ?>

<?php if ($show) { ?>
<span class="error"><br />Il ticket &egrave; nuovamente visibile.<br /></span>
<?php } ?>

<br />

<?php
$tickets = $delegate->getTickets(999, isset($_GET['showall']) ? TRUE : FALSE);
if ($tickets) {
	foreach ($tickets as $ticket) {
	$color = $delegate->getTicketColor($ticket['status']);
	?>
<div class="ticket <?php echo $color; ?>">
	<div class="left">
		<div class="secondline">[<strong>#<?php echo $ticket['id']; ?></strong>] <?php echo $ticket['title']; ?>
		
		<?php if ($color == 'blue') echo '&nbsp;<img src="'.$delegate->root.'img/loader.gif" border="0" alt="" />'; ?>
		</div>
		<span class="firstline"><?php echo $delegate->getTicketStatus($ticket['status'], $ticket['worker']); ?>. <strong><?php echo $delegate->getTicketRepliesCount($ticket['id']); ?> risposte</strong>. Aggiornato al <?php echo $delegate->helpers->toItalianDateTime($ticket['last_update']); ?> da <strong><?php echo $ticket['realname']; ?></strong> (Progetto: <?php echo $ticket['project_name']; ?>)</span>
	</div>
	<div class="right">
		<a href="<?php echo $delegate->root; ?>ticket/<?php echo $ticket['id']; ?>" class="button grey">Dettaglio</a>
	</div>
	<div class="clear"></div>
	

</div>
<?php } ?>
	<br /><a href="<?php echo $delegate->root; ?>list?showall=true">Mostra ticket nascosti</a>
<?php
}else echo 'Nessun ticket inserito.'; ?>


<?php $delegate->renderFooter(); ?>