<?php
/**
 * Support-Ticketing Mail Sender
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
Class MailSender {
	private $subject;
	private $body;
	private $emails;
	private $mailer;
	private $root;
	private $myname;
	
	public function __construct($root, $myname) {
		$this->mailer = 'From: Support <' . APPMAIL . '>' . "\r\n" .
		    'Reply-To: ' . APPMAIL . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		$this->root = $root;
		$this->myname = $myname;
	}
	
	public function prepare($ticket, $type, $info='') {
		switch ($type) {
			case 'inserted':
				if ($info == 'add') $this->subject = 'Nuovo ticket inserito';
				else $this->subject = 'Ticket modificato';
				$this->body = $this->myname." ha ".($info == 'add'? 'inserito un nuovo':'modificato un')." ticket per il progetto ".$ticket['project_name'].".\r\n\r\nTitolo: ".stripslashes($ticket['title']).
					"\r\n\r\nPer visualizzare il ticket, visita il seguente indirizzo:\r\n".$this->root.'ticket/'.$ticket['id'];
				break;
			case 'reply':
				if ($ticket['status'] == 'resolved') $this->subject = 'Ticket risolto';
				else if ($ticket['status'] == 'assigned') $this->subject = 'Ticket preso in carico';
				else $this->subject = 'Nuova risposta ricevuta';
				$this->body = $this->myname." ha inserito una risposta:\r\n".htmlentities(stripslashes($info['message'])).
					"\r\nPer visualizzare il ticket, visita il seguente indirizzo:\r\n".$this->root.'ticket/'.$ticket['id'];
				break;
			case 'closed':
				$this->subject = 'Richiesta chiusura ticket';
				$this->body = $this->myname." ha richiesto la chiusura di un ticket per il progetto ".$ticket['project_name'].".\r\n\r\nTitolo: ".$ticket['title'].
					"\r\n\r\nPer chiudere il ticket, visita il seguente indirizzo:\r\n".$this->root.'ticket/'.$ticket['id'];
			default:
		}
		$this->subject = '[Ticket #'.$ticket['id'].'] '. $this->subject .' - '.$ticket['project_name'];
		$this->body = $this->body . "\r\n\r\n" . "----------------\r\n".
			"Messaggio automatico inviato dalla piattaforma Support-Ticketing.\r\n".
			APPPATH . " - ". APPMAIL;
	}
	
	public function send($emails) {
		if (is_array($emails)) {
			foreach ($emails as $mail) {
				mail($mail['email'], $this->subject, $this->body, $this->mailer);
				if (EMAIL_LOGGING) file_put_contents('email.log', date('Y-m-d H:i:s').' Email sent to '.$mail['email'].' with subject '.$this->subject."\r\n", FILE_APPEND);
			}
		}else{
			mail($emails, $this->subject, $this->body, $this->mailer);
			if (EMAIL_LOGGING) file_put_contents('email.log', date('Y-m-d H:i:s').' Email sent to '.$mail['email'].' with subject '.$this->subject."\r\n", FILE_APPEND);
		}
	}
}