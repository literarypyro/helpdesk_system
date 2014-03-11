<?php
session_start();
?>
<?php
$_SESSION['helpdesk_page']="taskmonitor.php";
?>
<?php
require("db_page.php");
?>
<?php
ini_set("date.timezone","Asia/Kuala_Lumpur");
?>
<?php
if(isset($_POST['change_task'])){
	$msg=$_POST['client_name'];
	$msg="You have a new message";

	$filename  = "../helpdesk/data/helpdesk_".$_POST['change_staffer'].".txt";
		
	if(file_exists($filename)){
	}
	else {
		fopen($filename,'w');	
	}
	file_put_contents($filename,$msg);		
	
//	$db=new mysqli("localhost","root","","helpdesk_backup");
	$db=retrieveHelpdeskDb();
	$sendTime=date("Y-m-d H:i:s");
	
	$sql="update task set dispatch_staff='".$_POST['change_staffer']."',admin_time='".$sendTime."', status='Dispatched' where id='".$_POST['change_task']."'";
	$rs=$db->query($sql);	
	
	//$sql2="insert into forward_task (select id,client_name,division_id,unit_id,classification_id  from task where id='".$_POST['change_task']."')";
	//$rs=$db->query($sql2);	
	
	$sql3="insert into taskadmin(task_id,admin_id) values ('".$_POST['change_task']."','".$_SESSION['username']."')";
	$rs=$db->query($sql3);	
}

if(isset($_POST['delete_task'])){
	$db=retrieveHelpdeskDb();
	$sql="delete from task where id='".$_POST['delete_task']."'";
	$rs=$db->query($sql);
	$msg="Task deleted.";
}






if(isset($_POST['reassign_task'])){
	$msg=$_POST['client_name'];
	$msg="You have a new message";

	$filename  = "../helpdesk/data/helpdesk_".$_POST['reassign_staffer'].".txt";
		
	if(file_exists($filename)){
	}
	else {
		fopen($filename,'w');	
	}
	file_put_contents($filename,$msg);		
	
	$db=retrieveHelpdeskDb();
	$sendTime=date("Y-m-d H:i:s");
	
	$sql="update task set dispatch_staff='".$_POST['reassign_staffer']."',admin_time='".$sendTime."', status='Dispatched' where id='".$_POST['reassign_task']."'";
	$rs=$db->query($sql);
	


}


?>
<?php
//$db=new mysqli("localhost","root","","helpdesk_backup");
$db=retrieveHelpdeskDb();
$sql="select * from dispatch_staff inner join login  on dispatch_staff.id=login.username where dispatch_staff.id='".$_SESSION['username']."'";
$rs=$db->query($sql);
$userRow=$rs->fetch_assoc();
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta http-equiv="refresh" content="120;url=taskmonitor.php" />

<link rel="stylesheet" type="text/css" href="admin_staff.css" />
<!--
<meta http-equiv="refresh" content="5;url=scanMessages.php" />
-->
<script type="text/javascript" src="prototype.js"></script>
	<body style="background-image:url('body background.jpg');">
	<?php 
	require("web_header.php");
	?>
		<div id='alert_sound'></div>
	<!--Heading Table-->
	<table  width="100%"  bgcolor="#FFFFFF" cellpadding="5px" bordercolor="#CCCCCC" style="border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px">
<tr>
	<th colspan=2 class='subheader' align=right>Administrator: <font color=black><?php echo $userRow['staffer']; ?></font></th>
</tr>

<tr>
	<?php 
	require("admin_sidebar.php");
	//background-color:#66ceae; 
	?>
	<td width="85%" rowspan=2 valign="top"  style="background-color:hsl(225,80%,70%); border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color:black;" bordercolor="#FF6600">

<table id='cssTable' width=100%>
<tr>
<th colspan=6>
<h2>Online Client Requests</h2>
</th>
</tr>
<tr>
<th>Reference Number</th>
<th>Client Name</th>
<th>Office</th>
<th>Unit Type</th>
<th>Problem Concern</th>
<th>Request Time</th>
</tr>
<?php
//$db=new mysqli('localhost','root','','helpdesk_backup');
$db=retrieveHelpdeskDb();
$sql="select (select count(*) from forward_admin where id=task.id) as forward_count,task.* from task where (select count(*) from accomplishment where task_id=task.id)=0 and (dispatch_staff is null or dispatch_staff='') order by dispatch_time desc";
$rs=$db->query($sql);


$sql2="update task set status='Administrator' where (select count(*) from accomplishment where task_id=task.id)=0 and (dispatch_staff is null or dispatch_staff='')";

$rs2=$db->query($sql2);



$nm=$rs->num_rows;
$count=$nm;
$routing_Option="<select name='change_task'>";
$delete_option="<select name='delete_task'>";
for($i=0;$i<$nm;$i++){
	$row=$rs->fetch_assoc();
	
	$sql2="select * from computer where id='".$row['unit_id']."'";
	$rs2=$db->query($sql2);
	$row2=$rs2->fetch_assoc();
	
	$sql3="select * from classification where id='".$row['classification_id']."'";
	$rs3=$db->query($sql3);
	$row3=$rs3->fetch_assoc();
		
?>
<tr>
	
	<?php
	if($row['forward_count']>0){
		$tableStyle=" style='background-color:red;' ";
	}
	else {
		$tableStyle="";
	}
	?>
	<td <?php echo $tableStyle; ?>><font color=""><b><?php echo $row['reference_number']; ?></b></font></td>
	<td <?php echo $tableStyle;  ?>><font color=""><?php echo $row['client_name']; ?></font></td>
	<td <?php echo $tableStyle;  ?>><font color=""><?php echo $row['division_id']; ?></font></td>
	<td <?php echo $tableStyle;  ?>><font color=""><?php echo $row2['unit']; ?></font></td>
	<td <?php echo $tableStyle;  ?>><font color=""><?php echo $row3['type'].", ".$row['problem_details']; ?></font></td>
	<td <?php echo $tableStyle;  ?>><font color=""><?php echo date("F d, Y h:ia",strtotime($row['dispatch_time'])); ?></font></td>
</tr>
<?php
	$routing_Option.="<option value='".$row['id']."'>".$row['reference_number']."</option>";
	$delete_option.="<option value='".$row['id']."'>".$row['reference_number']."</option>";
	
}
$routing_Option.="</select>";
$delete_option.="</select>";
?>

</table>
<br>
<?php
$sql2="delete from forward_admin";
$rs2=$db->query($sql2);
?>
<?php 
if($count>0){
?>
<form action='taskmonitor.php' method='post'>
<table>
<tr>
<td>Assign Task: <?php echo $routing_Option; ?>
<select name='change_staffer'>
<?php
//$db=new mysqli('localhost','root','','helpdesk_backup');
$db=retrieveHelpdeskDb();
$sql="select * from dispatch_staff inner join login on dispatch_staff.id=login.username";

$rs=$db->query($sql);
$nm=$rs->num_rows;
for($i=0;$i<$nm;$i++){

$row=$rs->fetch_assoc();
?>
	<option value='<?php echo $row['id']; ?>'><?php echo $row['staffer']; ?></option>
<?php

}

?>
</select>

<input type=submit value='Assign' />
</td>
</tr>
</table>
</form>
<form action='taskmonitor.php' method='post'>
<table>
<tr>
<td>Delete (Redundant) Tasks:<?php echo $delete_option; ?>
<input type=submit value='Delete' />

</td>
</tr>
</table>
</form>
<?php
}
?>
<br>

<table id='cssTable' width=100%>
<tr>
<th colspan=7><h2>Re-Assign Client Requests</h2></th>
</tr>
<tr>
<th>Reference Number</th>
<th>Client Name</th>
<th>Office</th>
<th>Unit Type</th>
<th>Problem Concern</th>
<th>Dispatch Time</th>
<th>Dispatch Staff</th>

</tr>

<?php
$yearDate=date("Y-m-d",strtotime(date("Y-m-d")."-1 year"));


$db=retrieveHelpdeskDb();
$sql="select (select staffer from dispatch_staff where id=task.dispatch_staff) as staffer,(select count(*) from forward_admin where id=task.id) as forward_count,task.* from task inner join dispatch_staff on task.dispatch_staff=dispatch_staff.id where (select count(*) from accomplishment where task_id=task.id)=0 and (dispatch_staff is not null) and (dispatch_staff not in ('')) and dispatch_time>'".$yearDate."' order by dispatch_time desc";

$rs=$db->query($sql);
$nm=$rs->num_rows;
$count=$nm;
$routing_Option="<select name='reassign_task'>";
$delete_option="<select name='delete_task'>";

for($i=0;$i<$nm;$i++){
	$row=$rs->fetch_assoc();
	
	$sql2="select * from computer where id='".$row['unit_id']."'";
	$rs2=$db->query($sql2);
	$row2=$rs2->fetch_assoc();
	
	$sql3="select * from classification where id='".$row['classification_id']."'";
	$rs3=$db->query($sql3);
	$row3=$rs3->fetch_assoc();
	
	
?>

<tr>
	
	<?php
	if($row['forward_count']>0){
		$tableStyle=" style='background-color:red;' ";
	}
	else {
		$tableStyle="";
	}
	?>
	<td <?php echo $tableStyle; ?>><font color=""><b><?php echo $row['reference_number']; ?></b></font></td>
	<td <?php echo $tableStyle; ?>><font color=""><?php echo $row['client_name']; ?></font></td>
	<td <?php echo $tableStyle; ?>><font color=""><?php echo $row['division_id']; ?></font></td>
	<td <?php echo $tableStyle; ?>><font color=""><?php echo $row2['unit']; ?></font></td>
	<td <?php echo $tableStyle; ?>><font color=""><?php echo $row3['type'].", ".$row['problem_details']; ?></font></td>
	<td <?php echo $tableStyle; ?>><font color=""><?php echo date("F d, Y h:ia",strtotime($row['admin_time'])); ?></font></td>
	<td <?php echo $tableStyle; ?>><font color=""><?php echo $row['staffer']; ?></font></td>

</tr>
<?php
	$routing_Option.="<option value='".$row['id']."'>".$row['reference_number']."</option>";
	$delete_option.="<option value='".$row['id']."'>".$row['reference_number']."</option>";

}

$delete_option.="</select>";

$routing_Option.="</select>";
?>

</table>
<?php 
if($count>0){
?>
<table align=center>
<tr>
<td>
<input type=button value='Generate Printout of Report' onclick='window.open("report generation.php")' />
</td>
</tr>
</table>
<form action='taskmonitor.php' method='post'>
<table>
<tr>
<td>Re-Assign Task: <?php echo $routing_Option; ?>
<select name='reassign_staffer'>
<?php
//$db=new mysqli('localhost','root','','helpdesk_backup');
$db=retrieveHelpdeskDb();
$sql="select * from dispatch_staff inner join login on dispatch_staff.id=login.username";

$rs=$db->query($sql);
$nm=$rs->num_rows;
for($i=0;$i<$nm;$i++){

$row=$rs->fetch_assoc();
?>
	<option value='<?php echo $row['id']; ?>'><?php echo $row['staffer']; ?></option>
<?php

}

?>
</select>
<input type=submit value='Assign' />
</td>
</tr>
</table>
</form>
<form action='taskmonitor.php' method='post'>
<table>
<tr>
<td>Delete (Redundant) Tasks:<?php echo $delete_option; ?>
<input type=submit value='Delete' />

</td>
</tr>

</table>
</form>


<?php
}
?>

<br><br><br><br>


<?php 
$filename  = 'data/helpdesk_file.txt';

//if(file_exists($filename)){
	$modify=filemtime($filename);

//}
//else {
	//$modify=0;
//}
/*
echo "
<script type=\"text/javascript\">
var Comet = Class.create();
Comet.prototype = {

  timestamp: ".$modify.",
  url: './taskscanner.php',
  noerror: true,

  initialize: function() { },

  connect: function()
  {
    this.ajax = new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'timestamp' : this.timestamp },
      onSuccess: function(transport) {
        // handle the server response
        var response = transport.responseText.evalJSON();
        this.comet.timestamp = response['timestamp'];
        this.comet.handleResponse(response);
        this.comet.noerror = true;
      },
      onComplete: function(transport) {
        // send a new ajax request when this request is finished
        if (!this.comet.noerror)
          // if a connection problem occurs, try to reconnect each 5 seconds
          setTimeout(function(){ comet.connect() }, 5000); 
        else
          this.comet.connect();
        this.comet.noerror = false;
      }
    });
    this.ajax.comet = this;
  },

  disconnect: function()
  {
  },

  handleResponse: function(response)
  {
	document.getElementById('alert_sound').innerHTML=\"<embed src='doorbell.wav' type='application/x-mplayer2' autostart='true' width=0 height=0  >\";

	alert(\"You have a new message!\");
	window.location.href='taskmonitor.php';
	},

  doRequest: function(request)
  {
    new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'msg' : request }
    });
  },
  
  refresh: function()
  {
	comet.doRequest($('word').value);return false;

  }
  
  
  
}
var comet = new Comet();
comet.connect();
</script>
";
*/
?>	</td>
	</tr>
	</table>
</body>
</html>