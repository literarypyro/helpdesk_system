<?php
session_start();
?>
<?php
ini_set("date.timezone","Asia/Kuala_Lumpur");
?>
<?php
require("form functions.php");
require("db_page.php");
?>
<?php
$_SESSION['helpdesk_page']="task_printout.php";
?>
<?php
$updated=0;
if(isset($_POST['accid'])){
	$loginHour=adjustTime($_POST['loginamorpm'],$_POST['loginHour']);
	$loginMinute=$_POST['loginMinute'];
	$loginDay=$_POST['loginYear']."-".$_POST['loginMonth']."-".$_POST['loginDay'];
	$login_date=$loginDay." ".$loginHour.":".$loginMinute.":00";
	$actionTaken2=$_POST['action_taken'];
	$recommendation2=$_POST['recommendation'];
	$status2=$_POST['request_status'];
	
//	$db=new mysqli("localhost","root","","helpdesk_backup");
	$db=retrieveHelpdeskDb();
	$sql="update accomplishment set action_taken='".$actionTaken2."',recommendation='".$recommendation2."',accomplishment.status='".$status2."',accomplish_time='".$login_date."' where id='".$_POST['accid']."'"; 
	$rs=$db->query($sql);
	$updated++;
	
	}
?>
<?php
//$db=new mysqli("localhost","root","","helpdesk_backup");
$db=retrieveHelpdeskDb();

$sql="select * from dispatch_staff inner join login  on dispatch_staff.id=login.username where dispatch_staff.id='".$_SESSION['username']."'";
$rs=$db->query($sql);
$userRow=$rs->fetch_assoc();
?>
<link rel="stylesheet" type="text/css" href="helpdesk_staff2.css" />
<script language='javascript'>
function selectOption(elementName,elementValue){
	var elm=document.getElementById(elementName);

	for(i=0;i<elm.options.length;i++){
		if((elm.options[i].value)==(elementValue)){
			elm.options[i].selected=true;
		}
	}

}
</script>
<title>Update Helpdesk Staff Status</title>
	<body style="background-image:url('body background.jpg');">

	<?php 
	require("web_header.php");
	?>

<table width="100%"  bgcolor="#FFFFFF" cellpadding="5px" bordercolor="#CCCCCC" style="border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px">
<tr>
	<th style='border: 1px solid gray;background-color: #00cc66;color: white;' colspan=2 align=right>Computer Section Personnel: <font color=black><?php echo $userRow['staffer']; ?></font></th>
</tr>

<tr>
	<?php 
	require("helpdesk_sidebar.php");
	//background-color:#66ceae; 
	?>
	<td width="85%" rowspan=2 valign="top"  style="background-color:#66ceae; border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color:black;" bordercolor="#FF6600">

	
	<form action='task_printout.php' method='post'>
	<table>
	<tr>
	<th style='border: 1px solid gray;background-color: #00cc66;color: white;'>Prepare Printout of Task:</th><th style='border: 1px solid gray;background-color: #00cc66;color: #bd2031;'> 
	<select name='task_id'>
		<?php
		//$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();
		$sql="select * from task inner join accomplishment on task.id=accomplishment.task_id where dispatch_staff='".$_SESSION['username']."' and printed='false' order by task_id desc";

		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		$count=$nm;
		for($i=0;$i<$nm;$i++){
			$row=$rs->fetch_assoc();
	?>	
		<option value='<?php echo $row['task_id']; ?>' <?php if($_POST['taskid']==$row['task_id']){ echo "selected"; } ?>  <?php if($_POST['task_id']==$row['task_id']){ echo "selected"; } ?>><?php echo $row['reference_number']; ?></option>
		<?php	
		}
	?>	
	</select>
	</th>
	<th><input <?php if($count==0){ ?> disabled=true <?php } ?>type=submit value='Submit' /><input type=hidden name='user_name' value='<?php echo $_POST['login_user']; ?>' /></th>

	</tr>
	</table>
	<?php
	if($updated>0){
		echo "<font color=red>Accomplishment was updated. Resubmit to view changes.";
	}
	?>
	</form>
	<?php
	if(isset($_POST['task_id'])){
		$tsk=$_POST['task_id'];
		
		$_SESSION['helpdesk_printout']=$_POST['task_id'];
		
		$db=retrieveHelpdeskDb();
		$sql2="select *,accomplishment.status as task_stat,accomplishment.id as accid from task inner join accomplishment on task.id=accomplishment.task_id where task.id='".$tsk."'";

		
		
		$rs2=$db->query($sql2);
		$nm2=$rs2->num_rows;
		if($nm2>0){
		$row2=$rs2->fetch_assoc();
		$referenceNumber=$row2['reference_number'];
		
		$sql3="select * from computer where id='".$row2['unit_id']."'";
		$rs3=$db->query($sql3);
		$row3=$rs3->fetch_assoc();
	
?>
	<form action='task_printout.php' method='post'>
	<table id='alterTable' width=100%>
	<tr>
	<th colspan=5><h2><?php echo $referenceNumber; ?></h2></th>
	</tr>
	<tr>
	<th>Client Name</th>
	<th>Dispatch Time</th>
	<th>Problem Details</th>
	<th>From Office</th>
	<th>Unit Type</th>
	</tr>
	<tr>
	<td><?php echo $row2['client_name']; ?></td>
	<td><?php echo date("F d, Y h:ia",strtotime($row2['dispatch_time'])); ?></td>
	<td><?php echo $row2['problem_details']; ?></td>
	<td><?php echo $row2['division_id']; ?></td>
	<td><?php echo $row3['unit']; ?></td>
	</tr>
	</table>
	<?php
	$actionTaken=$row2['action_taken'];
	$recommendation=$row2['recommendation'];
	$status=$row2['task_stat'];
	$amorpm=date("A",strtotime($row2['accomplish_time']));
	$hour=date("h",strtotime($row2['accomplish_time']));

	$minute=date("i",strtotime($row2['accomplish_time']));
	$day=date("d",strtotime($row2['accomplish_time']));
	$month=date("m",strtotime($row2['accomplish_time']));
	$year=date("Y",strtotime($row2['accomplish_time']));


	?>
	<table align=center  id='alterTable' style='border: 1px solid gray'>
	<tr><th colspan=4>Fill-in Accomplishment</th></tr>
	<tr>
		<td valign=top>
		Action Taken:
		</td>
		<td><textarea name='action_taken'  cols=30 rows=5><?php echo $actionTaken; ?></textarea>
		</td>
		<td valign=top>
		Recommendation:
		</td>
		<td><textarea name='recommendation' cols=30 rows=5><?php echo $recommendation; ?></textarea>
		</td>

	</tr>	
	<tr>
		<td>
		Date:
		</td>
		<td>
		<?php
		retrieveMonthListHTML("loginMonth");
		retrieveDayListHTML("loginDay");
		retrieveYearListHTML("loginYear");
		?>
		</td>
		<td align=right>
		Time:
		</td>
		<td>
		<?php
		retrieveHourListHTML("loginHour");
		retrieveMinuteListHTML("loginMinute");
		retrieveShiftListHTML("loginamorpm");
		?>
		</td>
	</tr>
	<tr>
		<td align=center colspan=4>Status of Request: <input type='text' name='request_status' size=30 value='<?php echo $status; ?>' /></td>
	</tr>
	<tr>
		<td colspan=4 align=center><input <?php if($count==0){ ?> disabled=true <?php } ?>type=submit value='Edit' /><input type=button value="Create Printout" onclick="alert('Generating printout... Press okay to proceed.');window.open('print_outline3.php'); self.focus();" /><input type=hidden name='accid' value='<?php echo $row2['accid']; ?>' /><input type=hidden name='taskid' value='<?php echo $row2['task_id']; ?>' /></td>
	</tr>
	</table>
	</form>	
<?php
	echo "
	<script language='javascript'>
	selectOption('loginamorpm','".$amorpm."');
	selectOption('loginHour','".($hour*1)."');
	selectOption('loginMinute','".($minute*1)."');
	selectOption('loginYear','".($year*1)."');
	selectOption('loginMonth','".($month*1)."');
	selectOption('loginDay','".($day*1)."');
	</script>
	";
		
		}
	}
	
	
	?>	

	</td>
</tr>
</table>
</body>