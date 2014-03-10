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
$_SESSION['helpdesk_page']="dispatchTrack.php";

?>

<?php
if((isset($_POST['location']))&&(isset($_POST['task_id']))){
	if($_POST['location']==""){
		$loginMinute=$_POST['loginMinute'];
		$loginHour=adjustTime($_POST['loginamorpm'],$_POST['loginHour']);
		$loginDay=$_POST['loginYear']."-".$_POST['loginMonth']."-".$_POST['loginDay'];
		$login_date=$loginDay." ".$loginHour.":".$loginMinute.":00";
		
		//$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();
		$sql="insert into dispatch_track(dispatch_staffer,login_time,location,task_id) values ('".$_SESSION['username']."','".$login_date."',\"".$_POST['location']."\",'".$_POST['task_id']."')";
		$rs=$db->query($sql);

		$update="update task set status='Work Undergoing' where id='".$_POST['task_id']."'";
		$rs2=$db->query($update);
		
		
		echo "Dispatch staffer has updated his status.<br>";	
	}

}

?>
<?php
//$db=new mysqli("localhost","root","","helpdesk_backup");
$db=retrieveHelpdeskDb();
$sql="select * from dispatch_staff inner join login  on dispatch_staff.id=login.username where dispatch_staff.id='".$_SESSION['username']."'";
$rs=$db->query($sql);
$userRow=$rs->fetch_assoc();

//from cssTable
//background-color: black;
//color:yellow;

?>
<script language="javascript">
function markLocation(elementa){
	if(elementa.value=="OTHER"){
	document.getElementById('location').disabled=false;	
	}
	else {
	//document.getElementById('location').disabled=true;	
	document.getElementById('location').value=elementa.value;
	}
//	alert(document.getElementById('location').value);
	//	=elementa.value;
}


</script>
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
	<form action='dispatchTrack.php' method='post'>
	<table id='cssTable' align=center style='border: 1px solid gray'>
	<tr><th colspan=2>Report Staff Location</th></tr>
	<tr>
		<td>
		Log-in Date:
		</td>
		<td>
		<?php
		retrieveMonthListHTML("loginMonth");
		retrieveDayListHTML("loginDay");
		retrieveYearListHTML("loginYear");
		?>
		</td>
	</tr>	
	<tr>
		<td>
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
		<td>Enter Current Location:</td>
		<td>
		<input name='location' id='location' type='text' size=40 />
		</td>
	</tr>
	<tr>
	<td></td>
	<td>		
		<select name='presetLocation' id='presetLocation' onchange='markLocation(this)'>
		<?php
			$db=retrieveHelpdeskDb();
			$sql="select * from division";
			$rs=$db->query($sql);
			$nm=$rs->num_rows;
			for($i=0;$i<$nm;$i++){
				$row=$rs->fetch_assoc();
		?>	
			<option value="<?php echo $row['division_short']; ?>"><?php echo $row['division_name']; ?></option>	
			
		<?php	
			}
		?>
		<option value="OTHER" selected>OTHER</option>
		</select>
</td>
	</tr>
	<tr>
		<td>Enter Task:</td>
		<td>
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
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center><input <?php if($count==0){ ?> disabled=true <?php } ?> type=submit value='Submit' /><input type=hidden name='user_name' value='<?php echo $_POST['login_user']; ?>' /></td>
	</tr>
	</table>
	</form>	
</td>
</tr>
</table>
</body>