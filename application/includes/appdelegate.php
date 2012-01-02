<?php
/**
 * Support-Ticketing App Delegate
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
require_once('Mint/Config.php');
require_once('helpers.php');
require_once('mailsender.php');

Class AppDelegate {

	public $me;
	public $root = '';
	public $statuses = array('inserted', 'assigned', 'discussion', 'resolved', 'closed', 'reply');
	public $helpers;
	
	private $db;
	private $layout_folder = '../layout/';
	private $attach_folder = '../attach/';
	private $tickets;
	private $mailSender;
	private $notifications;
	
	
	public function __construct($needsLogin=true, $notifications=true)
	{
		$this->root = APPPATH;
		$this->db = new Db();
		
		if ($needsLogin && !$this->isLoggedIn()) {
			$this->redirect('login?redirect='.urlencode($_SERVER['SCRIPT_URI']));
		}else{
			$this->myProjects();
			$this->mailSender = new MailSender($this->root, $_SESSION['me']['realname']);
		}
		
		ini_set('error_reporting', E_ALL ^ E_NOTICE);
		$this->helpers = new Helpers();
		$this->tickets = array();
		$this->notifications = $notifications;
	}
	
	public function isMobile()
	{
		if (!isset($_SESSION['device_type'])) {
			//$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
			$isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
			$isiPod = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPod');
			$isAndroid = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'Android');
			if ($isiPhone || $isIpod || $isAndroid) $_SESSION['device_type'] = true;
		}
		return $_SESSION['device_type'];
	}
	
	public function renderHeader()
	{
		include_once($this->layout_folder.'header.phtml');
	}
	
	public function renderFooter()
	{
		include_once($this->layout_folder.'footer.phtml');
	}

	public function isLoggedIn()
	{
		if (isset($_SESSION['me'])) return true;
		else return false;
	}
	
	public function doLogout()
	{
		session_destroy();
	}
	
	public function tryLogin($data)
	{
		if (strlen($data['username']) && strlen($data['password'])) {
			$user = addslashes($data['username']);
			$pwd = addslashes($data['password']);
			$sql = "SELECT id, realname, isadmin FROM support_users ".
				   "WHERE username = '".$user."' AND password = '".$pwd."' LIMIT 1;";
			$this->db->query($sql);
			if ($this->db->numRows()) {
				//User found
				$row = $this->db->row();
				$userdata = array(
					'id' => $row['id'],
					'username' => $username,
					'realname' => $row['realname'],
					'isadmin' => (int)$row['isadmin']
				);
				$_SESSION['me'] = $userdata;
				
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function isAdmin()
	{
		if ($_SESSION['me']['isadmin'] == 1) return true;
		else return false;
	}
	
	public function redirect($page, $absolute=false)
	{
		if ($absolute) header('Location: '.$page);
		else header('Location: '.$this->root.$page);
		exit;
	}
	
	public function redirectToList()
	{
		$this->redirect('list');
	}
	
	
	public function myProjects()
	{
		if (!isset($_SESSION['me']['projects']) && isset($_SESSION['me'])) {
			$sql = "SELECT p.id as id, p.name as name FROM support_projects_relations r ".
			"INNER JOIN support_projects p ON r.project = p.id WHERE r.owner = ".$_SESSION['me']['id'].";";
			$this->db->query($sql);
			if ($this->db->numRows()) {
				while ($row = $this->db->row()) {
					$projects[$row['id']] = array(
						'name' => stripslashes($row['name']),
						'hash' => md5($_SESSION['me']['id'].$row['name'])
					);
				}
				$_SESSION['me']['projects'] = $projects;
				
				return $_SESSION['me']['projects'];
			}
		}else{
			return $_SESSION['me']['projects'];
		}
	}
	
	public function getAllUsers()
	{
		$sql = "SELECT id, realname FROM support_users ORDER BY realname ASC;";
		$this->db->query($sql);
		if ($this->db->numRows()) {
			while ($row = $this->db->row()) {
				$tmp[] = $row;
			}
			return $tmp;
		}
	}
	
	public function getProjectUsers($id, $idkey=false)
	{
		if (!$id) return false;
		$sql = "SELECT u.id, u.realname FROM support_projects_relations r ".
		"INNER JOIN support_users u ON r.owner = u.id WHERE r.project = ".(int)$id." ORDER BY realname ASC;";
		$this->db->query($sql);
		if ($this->db->numRows()) {
			while ($row = $this->db->row()) {
				if (!$idkey) $tmp[] = $row;
				else $tmp[$row['id']] = $row['realname'];
			}
			return $tmp;
		}
	}
	
	public function getProjectIdByHash($hash)
	{
		foreach ($this->myProjects() as $id => $arr) {
			if ($hash==$arr['hash']) return $id;
		}
	}
	
	public function clearProjectsCache()
	{
		unset($_SESSION['me']['projects']);
		return true;
	}
	
	public function clearProjectAssociations($project_id)
	{
		$sql = "DELETE FROM support_projects_relations WHERE project = ".(int)$project_id.";";
		return $this->db->query($sql);
	}
	
	public function addProjectAssociation($project_id, $user_id)
	{
		$data = array(
			'owner'		=> $user_id,
			'project'	=> $project_id
		);
		return $this->db->insert('support_projects_relations', $data);
	}
	
	public function getTickets($limit=15, $showHidden = FALSE)
	{
		if (isset($_SESSION['filter'])) {
			foreach ($_SESSION['me']['projects'] as $id => $arr) {
				if ($arr['hash'] == $_SESSION['filter']) {
					$filter = "= ".$id;
				}
			}
		}else{
			if ($this->myProjects()) {
				$filter = 'IN (' . implode(', ', array_keys($this->myProjects())) . ')';
			}else $filter = " = 0 ";
		}
		$sql = "SELECT t.id, t.project, t.worker, t.priority, t.data, t.title, t.status, u.realname, t.last_update FROM support_tickets t ".
			   "INNER JOIN support_users u ON t.owner = u.id ".
			   "WHERE ". ($showHidden ? '' : 'hidden != 1 AND ') ." project ".$filter." ORDER BY last_update DESC LIMIT ".$limit.";";
			
	    $this->db->query($sql);
	    while ($row = $this->db->row()) {
	    	$row['project_name'] = $_SESSION['me']['projects'][$row['project']]['name'];
	    	$row['title'] = stripslashes($row['title']);
	    	$tickets[] = $row;
	    }
	    return $tickets;
	}
	
	public function getTicket($id)
	{
		if ($id) {
			$id = (int)$id;
			if ($this->canEditTicket($id)) {
				$sql = "SELECT t.owner, t.project, t.data, t.title, t.status, t.worker, u.realname, t.description, t.attach, t.hidden FROM support_tickets t ".
					   "INNER JOIN support_users u ON t.owner = u.id WHERE t.id = ".$id." LIMIT 1;";
				$this->db->query($sql);
				if ($this->db->numRows()) {
					$row = $this->db->row();
					$row['id'] = $id;
					$row['title'] = stripslashes($row['title']);
					$row['description'] = stripslashes($row['description']);
					$row['project_name'] = $_SESSION['me']['projects'][$row['project']]['name'];
					return $row;
				}
			}
		}
	}
	
	public function saveTicket($data)
	{
		$canEdit = false;
		if (array_key_exists('id',$data)) {
			if ($this->canEditTicket($data['id'])) {
				$canEdit = true;
			}
		}else $canEdit = true;
		if ($canEdit) {
			if ($this->db->save('support_tickets', $data, 'id')) {
				if (!array_key_exists('id',$data)) {
					$data['id'] = $this->db->insertId();
					$tipo = 'add';
				}else $tipo = 'edit';
				$this->addAsyncronousMailer($data['id'], 'inserted', $tipo);
				return true;
			}
		}
		return false;
	}

	public function canEditTicket($id)
	{
		if (array_key_exists($id, $this->tickets)) {
			return $this->tickets[$id]=='true'?true:false;
		}else{
			$this->db->query("SELECT project FROM support_tickets WHERE id = ".(int)$id." LIMIT 1;");
			if ($this->db->numRows()) {
				$ticket = $this->db->row();
				if (array_key_exists($ticket['project'], $_SESSION['me']['projects'])) {
					$this->tickets[$id] = 'true';
					return true;
				}
			}
		}
		$this->tickets[$id] = 'false';
		return false;
	}
	
	public function hideTicket($id, $hide = TRUE)
	{
		$id = (int)$id;
		if ($this->canEditTicket($id)) {
			return $this->db->query("UPDATE support_tickets SET hidden = " . ($hide ? '1' : '0') ." WHERE id = ".$id." LIMIT 1;");
		}
		return false;
	}
	
	public function deleteTicket($id)
	{
		$id = (int)$id;
		if ($this->canEditTicket($id)) {
			$this->db->query("SELECT attach FROM support_replies WHERE ticket = ".$id.";");
			while ($row = $this->db->row()) {
				@unlink('../attach/'.$row['attach']);
			}
			$this->db->query("DELETE FROM support_replies WHERE ticket = ".$id.";"); 
			if ($this->db->query("DELETE FROM support_tickets WHERE id = ".$id." LIMIT 1;")) {
				return true;
			}
		}
		return false;
	}
	
	public function getTicketStatus($status, $worker)
	{
		switch ($status) {
			case 'assigned':
				return 'Ticket in lavorazione da '.$worker;
			case 'resolved':
				return 'Ticket risolto da '.$worker;
			case 'discussion':
				return 'Ticket in discussione';
			case 'inserted':
			default:
				return 'Ticket non assegnato';
		}
	}
	
	public function getTicketColor($status)
	{
		switch ($status) {
			case 'assigned':
				return 'blue';
			case 'resolved':
				return 'green';
			case 'discussion':
				return 'red';
			case 'inserted':
			default:
				return 'grey';
		}
	}
	
	public function saveFile($file)
	{
		$name = date('YmdHis_').str_replace(' ','-',$file['name']);
		if (move_uploaded_file($file['tmp_name'], $this->attach_folder.$name)) {
			return $name;
		}else return false;
	}

	
	public function getTicketReplies($id)
{
		$sql = "SELECT u.realname, r.data, r.description, r.attach, r.quotetime, r.completedtime FROM support_replies r ".
			   "INNER JOIN support_users u ON r.owner = u.id WHERE ticket = ".(int)$id." ORDER BY r.id ASC;";
		$this->db->query($sql);
		if ($this->db->numRows()) {
			$tmp = array();
			while ($row = $this->db->row()) {
				$tmp[]= array(
					'id'	=> $row['id'],
					'realname' => $row['realname'],
					'data' => $row['data'],
					'description' => stripslashes($row['description']),
					'attach' => $row['attach'],
					'quotetime' => $row['quotetime'],
					'completedtime' => $row['completedtime']
				);
			}
			return $tmp;
		}
	}
	
	public function getTicketRepliesCount($id)
	{
		$sql = "SELECT count(id) AS total FROM support_replies WHERE ticket = ".(int)$id." ORDER BY id ASC;";
		$this->db->query($sql);
		if ($this->db->numRows()) {
			$row = $this->db->row();
			return $row['total'];
		}
		return '0';
	}

	public function addProject($name)
	{
		if ($this->isAdmin()) {
			if ($this->db->insert('support_projects', array('name' => $name))) {
				$project_id = $this->db->insertId();
				//Relation
				$this->addProjectAssociation($project_id, $_SESSION['me']['id']);
				return TRUE;
			}
		}
	}

	public function deleteProject($id)
	{
		if ($this->isAdmin() && is_numeric($id)) {
			//Delete the project and the associations
			$this->db->query("DELETE FROM support_projects WHERE id = $id;");
			$this->db->query("DELETE FROM support_projects_relations WHERE project = $id;");

			//Delete all replies and tickets
			$this->db->query("DELETE FROM support_replies WHERE ticket in (SELECT id FROM support_tickets WHERE project = $id);");
			$this->db->query("DELETE FROM support_tickets WHERE project = $id;");
			return TRUE;
		}
	}
	
	public function addReply($description, $ticketId, $attach='', $quotetime = 0, $completedtime = 0)
	{
		if ($this->canEditTicket($ticketId)) {
			$data = array(
				'owner' => $_SESSION['me']['id'],
				'description' => $description,
				'ticket' => $ticketId,
				'data' => date('Y-m-d H:i:s'),
				'attach' => $attach
			);
			if ($quotetime) $data['quotetime'] = (int)$quotetime;
			if ($completedtime) $data['completedtime'] = (int)$completedtime;

			if ($this->db->insert('support_replies', $data)) {
				$this->db->query("UPDATE support_tickets SET last_update = '".date('Y-m-d H:i:s')."' WHERE id = ".$ticketId." LIMIT 1;");
				$infos = array(
					'message' => $description,
				);
				$this->addAsyncronousMailer($ticketId, 'reply', $infos);
			
				return true;
			}
		}
		return false;
	}
	
	public function changeTicketStatus($ticketId, $newStatus)
	{
		if (in_array($newStatus, $this->statuses)) {
			if ($this->canEditTicket($ticketId)) {
				$data = array(
					'status' => $newStatus,
					'worker' => ($newStatus == 'assigned' || $newStatus == 'resolved')?$_SESSION['me']['realname']:null
				);
				
				if ($this->db->update('support_tickets', $data, 'WHERE id = '.(int)$ticketId.' LIMIT 1;')) {
					return true;
				}
			}
		}
	}
	
	private function addAsyncronousMailer($ticketId, $type, $additional_infos='')
	{
		if (!$this->notifications) return;
		$jsonData = json_encode($additional_infos);

		$_SESSION['async_mailer'][] = array(
			'ticket'	=> $ticketId,
			'type'		=> $type,
			'info'		=> $jsonData
		);
		$_SESSION['javascript_mailer'] .= "sendAsyncMail();"; 
	}
	
	public function sendMail($ticketId, $type, $additional_infos='')
	{
		if (!in_array($type, $this->statuses)) return;
		$this->db->query("SELECT id, project, title, status FROM support_tickets WHERE id = ".(int)$ticketId." LIMIT 1;");
		$ticket = $this->db->row();
		$ticket['project_name'] = $_SESSION['me']['projects'][$ticket['project']]['name'];
		
		$sql = "SELECT u.email, u.realname FROM support_projects_relations r ".
			   "INNER JOIN support_users u ON r.owner = u.id WHERE notifications = 1 AND project = ".(int)$ticket['project']." AND owner != ".(int)$_SESSION['me']['id'].";";
			   
		$this->db->query($sql);
		if ($this->db->numRows()) {
			while ($info = $this->db->row()) {
				$emails[]= $info;
			}
			$this->mailSender->prepare($ticket, $type, $additional_infos);
			$this->mailSender->send($emails);
		}
	}
	
	public function hyperlink($text)
	{
		$pattern_url = '~(?>[a-z+]{2,}://|www\.)(?:[a-z0-9]+(?:\.[a-z0-9]+)?@)?(?:(?:[a-z](?:[a-z0-9]|(?<!-)-)*[a-z0-9])(?:\.[a-z](?:[a-z0-9]|(?<!-)-)*[a-z0-9])+|(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))(?:/[^\\/:?*"<>|\n]*[a-z0-9])*/?(?:\?[a-z0-9_.%]+(?:=[a-z0-9_.%:/+-]*)?(?:&[a-z0-9_.%]+(?:=[a-z0-9_.%:/+-]*)?)*)?(?:#[a-z0-9_%.]+)?~i';
/*
		return preg_replace($pattern_url,"<a href=\"\\0\">\\0</a>", $text);
		*/
		
		$tag = " rel=\"nofollow\"";

		$text = preg_replace( "/(?<!<a href=(\"|'))((http|ftp|http)+(s)?:\/\/[^<>\s]+[\w])/i", "<a target=\"_new\" class=\"httplink\" href=\"\\0\"" . $tag . ">\\0</a>", $text );

		$text = preg_replace( "/(?<!<a href=(\"|')http:\/\/)(?<!http:\/\/)((www)\.[^<>\s]+[\w])/i", "<a target=\"_new\" class=\"httplink\" href=\"http://\\0\"" . $tag . ">\\0</a>", $text );
		$text = preg_replace( "/(?<!<a href=(\"|')https:\/\/)(?<!http:\/\/)((www)\.[^<>\s]+[\w])/i", "<a target=\"_new\" class=\"httplink\" href=\"http://\\0\"" . $tag . ">\\0</a>", $text );
		
		// :)
		$text = str_replace(
			array(':)', ' :-)'),
			' <img src="'.$this->root.'img/smile/smile.gif" border="0" />',
			$text
		);
		
		// :D
		$text = str_replace(
			array(':D', ':-D'),
			' <img src="'.$this->root.'img/smile/bigsmile.gif" border="0" />',
			$text
		);
		
		// YES
		$text = str_replace(
			'(Y)',
			' <img src="'.$this->root.'img/smile/yes.gif" border="0" />',
			$text
		);
		
		// LOVE
		$text = str_replace(
			array('&lt;3', '(L)'),
			' <img src="'.$this->root.'img/smile/heart.gif" border="0" />',
			$text
		);
		
		return trim($text);
	
	}

	public function displayTime($duration = 0)
	{
		$duration_h = 0;
		$duration_m = 0;

		while ($duration) {
			if ($duration>59) {
				$duration_h+=1;
				$duration-=60;
			}else{
				$duration_m=$duration;
				$duration=0;
			}
		}
		if ($duration_m && $duration_h) {
			return $duration_h . ' '.($duration_h > 1 ? 'ore' : 'ora').' e ' . $duration_m . ' minuti';
		} else if ($duration_h && !$duration_m) {
			return $duration_h . ' '.($duration_h > 1 ? 'ore' : 'ora');
		} else {
			return $duration_m . ' minuti';
		}
	}
	
}