<?php
function retrieveHelpdeskDb(){
	//$db=new mysqli("comsec-aidz","admin","123456","helpdesk_system");
	$db=new mysqli("localhost","root","","helpdesk_system");

	return $db;

}
function localOnlyDb(){
	//$db=new mysqli("comsec-aidz","admin","123456","helpdesk_system");
	$db=new mysqli("localhost","root","","helpdesk_system");

	return $db;
}
?>