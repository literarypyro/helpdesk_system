<?php
session_start();
?>
<?php
$_SESSION['helpdesk_page']="taskmonitor.php";
?>
<?php
require("db_page.php");
if(isset($_POST['change_task'])){

	
	//$db=new mysqli("localhost","root","","helpdesk_backup");
	$db=retrieveHelpdeskDb();
	$sql="update task set status='Acknowledged' where id='".$_POST['change_task']."'";
	$rs=$db->query($sql);	
	
	$_SESSION['service_call']=$_POST['change_task'];
 	echo "
	<script language='javascript'>
	window.open('print_outline2.php');
	</script>"; 
}
?>
<?php
$db=retrieveHelpdeskDb();

//$db=new mysqli("localhost","root","","helpdesk_backup");
$sql="select * from dispatch_staff inner join login  on dispatch_staff.id=login.username where dispatch_staff.id='".$_SESSION['username']."'";
$rs=$db->query($sql);
$userRow=$rs->fetch_assoc();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="helpdesk_staff.css" />

<!--
<meta http-equiv="refresh" content="5;url=scanMessages.php" />
-->
<script type="text/javascript" src="prototype.js"></script>
	<body style="background-image:url('body background.jpg');">
	<?php 
	require("web_header.php");
	?>
	<div id='alert_sound'></div>

<!--	

	-->
	<!--Heading Table-->
	<table  width="100%"  bgcolor="#FFFFFF" cellpadding="5px" bordercolor="#CCCCCC" style="border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px">
<tr>
	<th colspan=2 class='subheader' align=right>Computer Section Personnel: <font color=black><?php echo $userRow['staffer']; ?></font></th>
</tr>

<tr>
	<?php 
	require("helpdesk_sidebar.php");
	//background-color:#66ceae; 
	?>
	<td width="85%" rowspan=2 valign="top"  style="background-color:hsl(225,80%,70%); border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color:black;" bordercolor="#FF6600">

<table id='cssTable' width=100%>
<tr>
<th colspan=6><h2>Online Client Requests</h2></th>
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
$db=retrieveHelpdeskDb();
//$db=new mysqli('localhost','root','','helpdesk_backup');
$sql="select (select count(*) from forward_task where id=task.id) as forward_count,task.* from task where (select count(*) from accomplishment where task_id=task.id)=0 and dispatch_staff='".$_SESSION['username']."' order by dispatch_time desc";

$rs=$db->query($sql);
$nm=$rs->num_rows;
$count=$nm;
$routing_Option="<select name='change_task'>";
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
		$tableStyle=" style='background-color:red;color:white;' ";
	}
	else {
		$tableStyle=" style='color:black;'";
	}
	?>
	<td <?php echo $tableStyle; ?>><b><?php echo $row['reference_number']; ?></b></font></td>
	<td <?php echo $tableStyle;  ?>><?php echo $row['client_name']; ?></td>
	<td <?php echo $tableStyle;  ?>><?php echo $row['division_id']; ?></td>
	<td <?php echo $tableStyle;  ?>><?php echo $row2['unit']; ?></td>
	<td <?php echo $tableStyle;  ?>><?php echo $row3['type']; ?></td>
	<td <?php echo $tableStyle;  ?>><?php echo date("F d, Y h:ia",strtotime($row['dispatch_time'])); ?></td>
</tr>
<?php
	$routing_Option.="<option value='".$row['id']."'>".$row['reference_number']."</option>";
}
$routing_Option.="</select>";
?>

</table>
<?php
$sql2="delete from forward_task";
$rs2=$db->query($sql2);
?>
<?php 
if($count>0){
?>
<form action='taskmonitor.php' method='post'>
<table>
<tr>
<th>Take Task/Generate Service Call:</th><th> <?php echo $routing_Option; ?>
<input type=submit value='Process' />
</th>
</tr>
</table>
</form>
<?php
}
?>
<br><br><br><br>


<?php 
$filename  = 'data/helpdesk_'.$_SESSION['username'].'.txt';

$modify=filemtime($filename);
$userId=$_SESSION['username'];

echo "
<script type=\"text/javascript\">

var Comet = Class.create();
Comet.prototype = {

  timestamp: ".$modify.",
  suffix: ".$userId.",
  url: './taskscanner.php',
  noerror: true,

  initialize: function() { },

  connect: function()
  {
    this.ajax = new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'timestamp' : this.timestamp, 'suffix' : this.suffix },
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
?>	</td>
	</tr>
	</table>
</body>
</html>