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
$_SESSION['helpdesk_page']="submitAccomplishment.php";

?>

<?php
if(isset($_POST['request_status'])){
	$loginHour=adjustTime($_POST['loginamorpm'],$_POST['loginHour']);
	$loginMinute=$_POST['loginMinute'];
	$loginDay=$_POST['loginYear']."-".$_POST['loginMonth']."-".$_POST['loginDay'];
	$login_date=$loginDay." ".$loginHour.":".$loginMinute.":00";

	$taskId=$_POST['task_id'];
	$actionTaken=$_POST['action_taken'];
	$recommendation=$_POST['recommendation'];
	$status=$_POST['request_status'];
	
//	$db=new mysqli("localhost","root","","helpdesk_backup");
	$db=retrieveHelpdeskDb();
	$sql="insert into accomplishment(task_id,action_taken,recommendation,status,accomplish_time) values ('".$taskId."',\"".$actionTaken."\",\"".$recommendation."\",\"".$status."\",'".$login_date."')";
	$rs=$db->query($sql);

	$sql2="update task set status='Finished' where id='".$taskId."'";
	$rs2=$db->query($sql2);	
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
	<form action='submitAccomplishment.php' method='post'>
	<table>
	<tr>
	<th style='border: 1px solid gray;background-color: #00cc66;color: white;'>Report on Client Request:</th><th style='border: 1px solid gray;background-color: #00cc66;color: #bd2031;'> 
	<select name='task_id'>
		<?php
		//$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();
		$sql="select (select count(*) from forward_task where id=task.id) as forward_count,task.* from task where (select count(*) from accomplishment where task_id=task.id)=0 and dispatch_staff='".$_SESSION['username']."' order by dispatch_time desc";
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		$count=$nm;
		for($i=0;$i<$nm;$i++){
			$row=$rs->fetch_assoc();
	?>	
		<option value='<?php echo $row['id']; ?>'><?php echo $row['reference_number']; ?></option>
		<?php	
		}
	?>	
	</select>
	</th>
	</tr>
	</table>
	<table id='cssTable' style='border: 1px solid gray'>
	<tr><th colspan=4>Fill-in Accomplishment</th></tr>
	<tr>
		<td valign=top>
		Action Taken:
		</td>
		<td><textarea name='action_taken' cols=30 rows=5></textarea>
		</td>
		<td valign=top>
		Recommendation:
		</td>
		<td><textarea name='recommendation' cols=30 rows=5></textarea>
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
		<td align=center colspan=4>Status of Request: <input type='text' name='request_status' size=30 /></td>
	</tr>
	<tr>
		<td colspan=4 align=center><input <?php if($count==0){ ?> disabled=true <?php } ?>type=submit value='Submit' /><input type=hidden name='user_name' value='<?php echo $_POST['login_user']; ?>' /></td>
	</tr>
	</table>
	</form>	
</td>
</tr>
</table>
</body>