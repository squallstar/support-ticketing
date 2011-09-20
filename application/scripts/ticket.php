<?php
/**
 * Support-Ticketing Ticket view
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(true);

//Form save
if (isset($_POST['act'])) {

	$html_from = array('<', '>');
	$html_to = array('&lt;', '&gt;');
	$_POST['description'] = str_replace($html_from, $html_to, $_POST['description']);

	if (!isset($_POST['edit'])) {
		//New
		$data = array(
			'data'			=> date('Y-m-d H:i:s'),
			'project'		=> (int)$_POST['project'],
			'owner'			=> $_SESSION['me']['id'],
			'title'			=> $_POST['title'],
			'description'	=> $_POST['description'],
			'status'		=> 'inserted',
			'worker'		=> null,
			'last_update'	=> date('Y-m-d H:i:s')
		);
	}else{
		//Edit
		$data = array(
			'id'			=> (int)$_POST['edit'],
			'title'			=> $_POST['title'],
			'data'			=> date('Y-m-d H:i:s'),
			'description'	=> $_POST['description'],
			'last_update'	=> date('Y-m-d H:i:s')
		);	
	}
	
	//File
	if (isset($_FILES['attach'])) {
		$name = $delegate->saveFile($_FILES['attach']);
		if ($name) {
			$data['attach'] = $name;
		}
	}
	
	if ($delegate->saveTicket($data)) {
		unset($_SESSION['filter']);
		$delegate->redirect('ticket/'.$data['id']);
	}else{
		$error = 'Impossibile salvare il ticket';
	}
	
	
}elseif (isset($_GET['id'])) {
	//Edit
	$title = 'Modifica ticket';
	$val = $delegate->getTicket($_GET['id']);
	
	$val['data'] = $delegate->helpers->toItalianDateTime($val['data']);
	
}else{
	//New
	$title = 'Nuovo ticket';
	
	$val = array(
		'data' => $delegate->helpers->toItalianDateTime(date('Y-m-d H:i:s')),
		'realname' => $_SESSION['me']['realname']
	);
	
}


$delegate->renderHeader();
?>

<script type="text/javascript">
$(document).ready(function() {
	$('#title').focus();
});
function checkForm() {
	if (!$('#title').val().length) {
		alert('E\' necessario compilare tutti i campi obbligatori.');
		return false;
	}
	document.editform.submit();
}
</script>

<h1><?php echo $title; ?></h1>



<br /><br />
<form action="<?php echo $delegate->root; ?>ticket/edit" method="post" class="editform" name="editform" id="editform" enctype="multipart/form-data">
	<fieldset>
		<input type="hidden" name="act" value="1" />
		<?php if (isset($_GET['id'])) { ?><input type="hidden" name="edit" value="<?php echo $_GET['id']; ?>" /><?php } ?>
		
		<?php if ($error) { ?><label></label><span class="left error"></span><div class="clear"></div><?php } ?>
		
		<label for="dataora">Data segnalazione</label>
		<input type="text" class="default" name="dataora" id="dataora" value="<?php echo $val['data']; ?>" readonly/>
		<div class="next"></div>
		
		<label for="segnalatore">Utente segnalatore</label>
		<input type="text" class="default" name="segnalatore" id="segnalatore" value="<?php echo $val['realname']; ?>" readonly/>
		<div class="next"></div>
		
		<label for="project"><strong>Progetto</strong></label>
		<select name="project" id="project">
			<?php foreach ($delegate->myProjects() as $id => $arr) { ?>
				<option value="<?php echo $id; ?>"<?php echo $val['project']==$id?' selected="selected"':''; ?>><?php echo $arr['name']; ?></option>
			<?php } ?>
		</select>
		<span class="mandatory">campo obbligatorio</span>
		<div class="next"></div>
		
		<label for="title"><strong>Titolo</strong></label>
		<input type="text" class="default" name="title" id="title" value="<?php echo $val['title']; ?>" maxlength="128"/>
		<span class="mandatory">campo obbligatorio</span>
		<div class="next"></div>
		
		<label for="description">Descrizione</label>
		<textarea class="default" name="description" id="description"><?php echo $val['description']; ?></textarea>
		<div class="next"></div>
		
		<label for="attach">Allegato</label>
		<input type="file" class="default" name="attach" id="attach"/>
		<div class="next"></div>
		
		<?php
		if ($val['attach']) { ?>
		<label></label>
		<div class="left">&nbsp;Attualmente hai allegato il file: <strong><a href="<?php echo $delegate->root.'attach/'.$val['attach']; ?>" target="_blank"><?php echo substr($val['attach'], 15); ?></a></strong></div>
		<div class="clear"></div>
		<br /><br />
		<?php } ?>
		
		<label></label>
		<div class="left">
			<?php if (isset($_GET['id'])) { ?>
			<a href="<?php echo $delegate->root; ?>ticket/<?php echo $_GET['id']; ?>" class="button blue" onclick="return confirm('Vuoi scartare le modifiche e tornare alla visualizzazione del ticket?');">Torna al dettaglio</a>
			<?php } else { ?>
			<a href="<?php echo $delegate->root; ?>list" class="button blue" onclick="return confirm('Vuoi scartare le modifiche e tornare alla lista?');">Torna alla lista</a>
			<?php } ?>
			&nbsp;&nbsp;&nbsp;<a href="javascript:checkForm();" class="button">Salva ticket</a>
		</div>
		<div class="clear"></div>
	</fieldset>
</form>



<?php $delegate->renderFooter(); ?>