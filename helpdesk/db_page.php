<?php
function retrieveHelpdeskDb(){
	//$db=new mysqli("comsec-aidz","admin","123456","helpdesk_system");
	$db=new mysqli("localhost","root","","helpdesk_system");

	//if(isNullDb($db)=="false"){
		//$db=new mysqli("","root","123456","helpdesk_backup");
	//}
	return $db;

}
function localOnlyDb(){
	//$db=new mysqli("comsec-aidz","admin","123456","helpdesk_system");

	$db=new mysqli("localhost","root","","helpdesk_system");

	//if(isNullDb($db)=="false"){
		//$db=new mysqli("localhost","root","123456","helpdesk_backup");
//	}
	return $db;
}


?>