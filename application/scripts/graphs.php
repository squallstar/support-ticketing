<?php
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(true, $_POST['notifications']=='no'?false:true);

if (!$delegate->isAdmin()) {
	$delegate->redirect('list');
}


$id = $delegate->getProjectIdByHash($_GET['hash']);
$_SESSION['filter'] = $_GET['hash'];

if (!$id) {
	$delegate->redirect('/list');
}

$projects = $delegate->myProjects();
$project = $projects[$id];

$tickets = $delegate->getTickets(999, TRUE);
$replies = $delegate->getProjectRepliesDates($id);
$preparedData = array();
$allDates = array();

//Primo giro, tutte le date (serie)
if (count($tickets)) {
	foreach ($tickets as $ticket) {
		$dataora = explode(' ', $ticket['last_update']);
		if (!isset($allDates[$dataora[0]])) {
			$allDates[$dataora[0]] = 0;
		}
	}
}
if (count($replies)) {
	foreach ($replies as $reply) {
		$dataora = explode(' ',$reply);
		if (!isset($allDates[$dataora[0]])) {
			$allDates[$dataora[0]] = 0;
		}
	}
}
ksort($allDates);
if ($allDates) {
	$closedTickets = $allDates;
	foreach ($tickets as $ticket) {
		$dataora = explode(' ', $ticket['last_update']);
		$closedTickets[$dataora[0]]++;
	}

	$repliesCount = $allDates;
	foreach ($replies as $reply) {
		$dataora = explode(' ', $reply);
		$repliesCount[$dataora[0]]++;
	}
}





$delegate->renderHeader();
?>
<script src="<?php echo $delegate->root; ?>javascript/highcharts.js" type="text/javascript"></script>

<h1><?php echo $project['name']; ?>: GRAFICO</h1>

<a href="<?php echo $delegate->root; ?>manage/projects" class="button blue">Torna alla lista progetti</a>
<br /><br /><br />

<div id="resolved_tickets"></div>

<?php if (count($closedTickets) || count($repliesCount)) { ?>
<script type="text/javascript">

var chart1; // globally available
$(document).ready(function() {
  chart1 = new Highcharts.Chart({
     chart: {
        renderTo: 'resolved_tickets',
        type: 'line'
     },
     title: {
        text: 'Riepilogo ticket risolti'
     },
     xAxis: {
     	categories: <?php echo json_encode(array_keys($allDates)); ?>
     },
     yAxis: {
        title: {
     		text: 'Quantita\''
     	}
     },
     series: [{
        name: 'Ticket risolti',
        data: <?php echo json_encode(array_values($closedTickets)); ?>
     },
     {
        name: 'Risposte ricevute',
        data: <?php echo json_encode(array_values($repliesCount)); ?>
     }]
  });
});
</script>
<?php } else { ?>
Nessun dato da visualizzare.
<?php } ?>




<?php $delegate->renderFooter(); ?>