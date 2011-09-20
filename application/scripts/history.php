<?php
/**
 * Support-Ticketing History view
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

$id = $_GET['id'];
if (!$id) $delegate->redirect('list');


if (isset($_POST['status']) && $delegate->isAdmin()) {
	$delegate->changeTicketStatus($id, $_POST['status']);
}

if (strlen($_POST['description'])) {

	$attach='';

	//File
	if (isset($_FILES['attach'])) {
		$name = $delegate->saveFile($_FILES['attach']);
		if ($name) {
			$attach = $name;
		}
	}

	$html_from = array('<', '>');
	$html_to = array('&lt;', '&gt;');
	$_POST['description'] = str_replace($html_from, $html_to, $_POST['description']);
	
	$done = $delegate->addReply($_POST['description'], $id, $attach);
	if (!$delegate->isAdmin() && $done) {
		$delegate->changeTicketStatus($id, 'discussion');
	}
}

if ($_GET['request']=='close') {
	if ($delegate->changeTicketStatus($id, 'closed')) {
		$delegate->sendMail($id, 'closed', '');
	}
}


$ticket = $delegate->getTicket($id);

$replies = $delegate->getTicketReplies($id);

$delegate->renderHeader();
?>

<h1>DETTAGLIO TICKET</h1>

<br />

<div class="ticket detail <?php echo $delegate->getTicketColor($ticket['status']); ?>">
	<div class="first">
		<em><strong><?php echo $ticket['project_name']; ?></strong><br /><br />
		Inserita da <strong><?php echo $ticket['realname']; ?></strong><br />
		<?php echo $delegate->helpers->toItalianDateTime($ticket['data']); ?><br /><br />
		
		<?php echo $delegate->getTicketStatus($ticket['status'], $ticket['worker']); ?><br />
		<?php echo count($replies); ?> risposte ricevute</em>
		
	</div>
	<div class="second">
		<h2><?php echo $ticket['title']; ?></h2>
		<span class="text13"><?php echo nl2br($delegate->hyperlink($ticket['description'])); ?></span>
		
		<?php
		if ($ticket['attach']) { ?>
		<br />&Egrave; presente un file allegato:&nbsp; <strong><a href="<?php echo $delegate->root.'attach/'.$ticket['attach']; ?>" target="_blank"><?php echo substr($ticket['attach'], 15); ?></a></strong>
		<?php } ?>
		
	</div>
	<div class="clear"></div>
</div>

<?php if (count($replies)) {
	echo '<div class="replytitle">DISCUSSIONE</div>';
	foreach ($replies as $reply) { $j++; ?>
<div class="ticket detail <?php if ($j==count($replies)) echo $delegate->getTicketColor($ticket['status']); ?>">
	<div class="first">
		<em><strong><?php echo $reply['realname'].($reply['realname']=='Nicholas Valbusa'?' <img src="'.$delegate->root.'img/adm.png" class="imgadm" />':''); ?></strong><br />
		<?php echo $delegate->helpers->toItalianDateTime($reply['data']); ?></em>
	</div>
	<div class="second margin">
		<span class="text13"><?php echo nl2br($delegate->hyperlink($reply['description'])); ?>
		<?php
		if ($reply['attach']) { ?>
		<br /><br />File allegato: <strong><a href="<?php echo $delegate->root.'attach/'.$reply['attach']; ?>" target="_blank"><?php echo substr($reply['attach'], 15); ?></a></strong>
		
		<?php } ?>
		</span>
	</div>
	<div class="clear"></div>
</div>
<?php }
} ?>

<div class="ticket detail">
	<div class="first reply">
	<strong>Risposta veloce</strong>
	</div>
	<div class="second">
		<form action="" method="post" class="replyform" enctype="multipart/form-data">
			<fieldset>
				<?php if ($delegate->isAdmin()) { ?>
				<select name="status" class="default">
					<option value="">Non cambiare lo stato del ticket</option>
					<option value="assigned">Assegna a me stesso</option>
					<option value="discussion">Metti in discussione</option>
					<option value="inserted">Stato generico (grigio)</option>
					<option value="resolved">Chiudi come risolta</option>
					<option value="closed">Chiudi</option>
				</select><br /><br />
				<select name="notifications" class="default">
					<option value="yes">Notifica la risposta via e-mail.</option>
					<option value="no">Non notificare la risposta.</option>
				</select><br /><br />
				<?php } ?>
				<textarea class="default" name="description" id="description"></textarea><br />
				Allega file <input type="file" class="default" name="attach" id="attach"/><br /><br />
				<input type="submit" class="button blue" value="Invia risposta" <?php if (!$delegate->isAdmin()) { ?>onclick="if($('#description').val()=='') { alert('Per inviare una risposta occorre digitare del testo nell\'apposita textarea.'); return false; }else{ return true; }"<?php } ?>/>
			</fieldset>
		</form>
	</div>
	<div class="clear"></div>
</div>


<br /><br /><br />

<a href="<?php echo $delegate->root; ?>list" class="button blue buttonmargin">Torna alla lista</a>&nbsp;&nbsp;&nbsp;
<a class="button buttonmargin" href="<?php echo $delegate->root; ?>ticket/edit/<?php echo $ticket['id']; ?>">Modifica ticket</a>&nbsp;&nbsp;&nbsp;
<?php if ($delegate->isAdmin()) { ?>
<a class="button buttonmargin" href="<?php echo $delegate->root; ?>list/delete-ticket/<?php echo $ticket['id']; ?>" onclick="return confirm('Eliminare questo ticket?');">Elimina ticket</a>
<?php }else{ ?>
<a class="button buttonmargin" href="<?php echo $delegate->root; ?>ticket/<?php echo $ticket['id']; ?>/close" onclick="return confirm('Inviare una e-mail per richiesta chiusura ticket?');">Richiedi chiusura ticket</a>
<?php } ?>


<?php $delegate->renderFooter(); ?>