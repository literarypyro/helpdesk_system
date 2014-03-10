<?php
session_start();
?>
<?php
require("form functions.php");
require("db_page.php");
?>
<?php
$_SESSION['helpdesk_page']="pendingRequests.php";

?>
<?php
//$db=new mysqli("localhost","root","","helpdesk_backup");
$db=retrieveHelpdeskDb();
$sql="select * from dispatch_staff inner join login  on dispatch_staff.id=login.username where dispatch_staff.id='".$_SESSION['username']."'";
$rs=$db->query($sql);
$userRow=$rs->fetch_assoc();
?>
<link rel="stylesheet" type="text/css" href="admin_staff.css" />
	<title>Pending Requests</title>
	<?php
		$conditionClause=" where ";
		
		$m=0;
		//$unitClause="";
		/**
		if($_POST['unit_filter']==""){
		}
		
		
		else {
			$unitClause=" unit_id='".$_POST['unit_filter']."'";
			$m++;
			
			if($m>1){
				$conditionClause.=" and ".$unitClause;
			}
			
			else {
				$conditionClause.=$unitClause;
			
			}
			
		}
		
		$issueClause="";
		if($_POST['issue_filter']==""){
		}
		else {
			$issueClause=" classification_id='".$_POST['issue_filter']."'";
			$conditionClause.=$issueClause;
		}
		*/
		$staffClause=" ";
		if($_POST['dispatch_staffer']==""){
			$staffClause=" (dispatch_staff is not null and dispatch_staff not in ('')) ";
			$m++;
			if($m>1){
				$conditionClause.=" and ".$staffClause;
			}
			else {
				$conditionClause.=$staffClause;
			
			}

		}
		else {
			$staffClause=" dispatch_staff='".$_POST['dispatch_staffer']."'";
			$m++;
			if($m>1){
				$conditionClause.=" and ".$staffClause;
			}
			else {
				$conditionClause.=$staffClause;
			
			}

		}

		if($_POST['date_filter']==""){
			$periodMonth=date("Y-m");
		
			$periodClause=" dispatch_time like '".$periodMonth."%%'";



			$m++;
			
			if($m>1){
				$conditionClause.=" and ".$periodClause;
				
			}
			else {
				$conditionClause.=$periodClause;
			
			}
			

		}
		else {
			if($_POST['date_filter']=="dRange"){
				$periodMonthbeginning=$_POST['fromYear']."-".(date("m",strtotime(date("Y")."-".$_POST['fromMonth'])))."-".$_POST['fromDay'];
				$periodMonthend=$_POST['toYear']."-".(date("m",strtotime(date("Y")."-".$_POST['toMonth'])))."-".$_POST['toDay'];
				
				$periodClause=" dispatch_time between '".$periodMonthbeginning." 00:00:00' and '".$periodMonthend." 23:59:59'";
			}
			else if($_POST['date_filter']=="daily"){
				$periodMonth=date("Y-m-d");

				$periodClause=" dispatch_time like '".$periodMonth."%%'";
			}
			else if($_POST['date_filter']=="weekly"){
				$periodMonthbeginning=$_POST['fromYear']."-".(date("m",strtotime(date("Y")."-".$_POST['fromMonth'])))."-".$_POST['fromDay'];
				$periodMonthend=$_POST['toYear']."-".(date("m",strtotime(date("Y")."-".$_POST['toMonth'])))."-".($_POST['toDay']*1+6);

				$periodClause=" dispatch_time between '".$periodMonthbeginning." 00:00:00' and '".$periodMonthend." 23:59:59'";
			}
			else if($_POST['date_filter']=="monthly"){
				$periodMonth=date("Y")."-".(date("m",strtotime(date("Y")."-".$_POST['fromMonth'])));
				$periodClause=" dispatch_time like '".$periodMonth."%%'";
			}

			else {
				$periodMonth=date("Y-m-d");
				$periodClause=" dispatch_time like '".$periodMonth."%%'";
			}

			$m++;

			
			if($m>1){
				$conditionClause.=" and ".$periodClause;
			}
			else {
				$conditionClause.=$periodClause;
			
			}
			echo $conditionClause;

		}
		$_SESSION['clause']=$conditionClause;
		//$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();
//		$db=localOnlyDb();
		$noClause=" (select count(*) from accomplishment where task_id=task.id)=0";
		if($conditionClause==""){
			$taskClause="where ".$noClause;
		}
		else {
			$taskClause=$conditionClause." and ".$noClause; 
		}
		$new_sql="select * from task ".$taskClause." order by dispatch_time desc";

		?>
	<script language="javascript">
	
	function openPrint(url){
		window.open(url);
	
	}
	</script>
	<body style="background-image:url('body background.jpg');">
		<div align=center><img src="mrt3.jpg" style="width:100%; height:250;" /></div>	
	<div align="right" width=100%><a style='color:red;	font: bold 14px "Trebuchet MS", Arial, sans-serif;' href="logout.php">Log Out</a></div>

	<!--Heading Table-->
	<table  width="100%"  bgcolor="#FFFFFF" cellpadding="5px" bordercolor="#CCCCCC" style="border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px">
<tr>
	<th colspan=2 align=right>Administrator: <font color=black><?php echo $userRow['staffer']; ?></font></th>
</tr>

<tr>
	<?php 
	require("admin_sidebar.php");
	//background-color:#66ceae; 
	?>
	<td width="85%" rowspan=2 valign="top"  style="background-color:#66ceae; border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color:black;" bordercolor="#FF6600">

	<form action='pendingRequests.php' method=post>

	<?php 

	$headingTable="
	<table align=center width=100%>
	<tr><th colspan=2><h2>UNPROCESSED REQUESTS</h2></th>
	</tr>
	</table>";	
	echo $headingTable;

	?>
	<b>Period Covered:</b>
	<select name='date_filter'>
		<option <?php if(($_POST['date_filter']=="dRange")||($_POST['date_filter']=="")) { echo "selected=true"; } ?> value='dRange'>Date Range:</option> 
		<option <?php if($_POST['date_filter']=="daily") { echo "selected=true"; } ?> value='daily'>Daily</option> 
		<option <?php if($_POST['date_filter']=="weekly") { echo "selected=true"; } ?>  value='weekly'>Weekly</option> 
		<option <?php if(($_POST['date_filter']=="monthly")) { echo "selected=true"; } ?> value='monthly' >Monthly</option> 
		<option <?php if($_POST['date_filter']=="yearly") { echo "selected=true"; } ?> value='yearly'>Annually</option> 
	</select>
	<b>From:</b> 	
		<?php
		retrieveMonthListHTML("fromMonth");
		retrieveDayListHTML("fromDay");
		retrieveYearListHTML("fromYear");
		?>
	
	<b>To:</b> 
		<?php
		retrieveMonthListHTML("toMonth");
		retrieveDayListHTML("toDay");
		retrieveYearListHTML("toYear");
		?>
	
	<br>
	<b>Filter Dispatch Staff</b>
	<select name='dispatch_staffer'>
		<option value=''>All Dispatch Staff</option>
	<?php
//		$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();
		//$db=localOnlyDb();
		$sql="select * from dispatch_staff";
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		for($i=0;$i<$nm;$i++){
			$row=$rs->fetch_assoc();
	?>	
			<option <?php if($_POST['dispatch_staffer']==$row['id']){ echo "selected=true"; } ?> value='<?php echo $row['id']; ?>'><?php echo $row['staffer']; ?></option>
	<?php	
		}
	?>		
	</select>
<!--
	<br>	-->
	<!--
	<b>Filter Client Request</b>
	<select name='dispatch_staffer'>
		<option value=''>All Dispatch Staff</option>
	</select>
	-->
	<br>
	<input type=submit value='Submit' />
	</form>
<br>
	<form action='submit.php' method=post>
	<?php
	$new_rs=$db->query($new_sql);
	$nm=$new_rs->num_rows;
	$count=$nm;
	if($nm>0)
	?>
	<table id='cssTable' width=100% >
	<tr>
	<th colspan=5><h2>Needing Accomplishments</h2></th>
	</tr>

	<tr>
		<th>Client Request Id</th>
		<th>Request Time</th>
		<th>Problem Details</th>
		<th>Dispatch Staff</th>
		<th>Status of Request</th>	
	</tr>	
	<?php
		for($i=0;$i<$nm;$i++){
		$row=$new_rs->fetch_assoc();
		
		
		$sql2="select * from dispatch_staff where id='".$row['dispatch_staff']."'";

		$rs2=$db->query($sql2);
		$row2=$rs2->fetch_assoc();
	
		$sql3="select * from task where id='".$row['task_id']."'";
		$rs3=$db->query($sql3);
		$row3=$rs3->fetch_assoc();
	/**
		$sql4="select * from computer where id='".$row['unit_id']."'";
		$rs4=$db->query($sql4);
		$row4=$rs4->fetch_assoc();
*/
		
		$_SESSION['sql_printout']=$new_sql;
		
		
		
	
	?>	
	<tr>
		<td><?php echo $row['reference_number']; ?></td>
		<td><?php echo date("F d, Y h:ia",strtotime($row['dispatch_time'])); ?></td>
		<td><?php echo $row['problem_details']; ?></td>
		<td><?php echo trim($row2['staffer']); ?></td>
		<td><?php echo $row['status']; ?></td>

	</tr>
	<?php
		}
	?>
	</table>
		<br>
	<?php
	$printClause=$_SESSION['clause'];

	if($printClause==""){
		$clause=" where printed='false'";
		
	}
	else {
		$clause=$printClause." and printed='false'";
	}
	
	$sql="select *,task.id as task_id from task inner join accomplishment on task.id=accomplishment.task_id ".$clause;
	$rs=$db->query($sql);
	$nm=$rs->num_rows;
	?>
	<table id='cssTable' width=100% >
	<tr>
	<th colspan=5><h2>Accomplishments not Printed</h2></th>
	</tr>

	<tr>
		<th>Client Request Id</th>
		<th>Request Time</th>
		<th>Problem Details</th>
		<th>Action Taken</th>
		<th>Dispatch Staff</th>
<!--
		<th>Status of Request</th>	
-->
	</tr>

	<?php	
	for($i=0;$i<$nm;$i++){
	$row=$rs->fetch_assoc();
	$sql2="select * from dispatch_staff where id='".$row['dispatch_staff']."'";
		$rs2=$db->query($sql2);
		$row2=$rs2->fetch_assoc();
	?>
	<tr>
		<td><a target='_blank' style='text-decoration:none;color:red' href='print_outline3.php?adminPrint=<?php echo $row['task_id']; ?>'><?php echo $row['reference_number']; ?></a></td>
		<td><?php echo date("F d, Y h:ia",strtotime($row['dispatch_time'])); ?></td>
		<td><?php echo $row['problem_details']; ?></td>
		<td><?php echo $row['action_taken']; ?></td>
		<td><?php echo trim($row2['staffer']); ?></td>
	</tr>	
	
	
	<?php
	}
	?>
	</table>
<!--
	<div align=center><input type=button value='Prepare Print Out' 
	<?php 
	//if($count==0){ 
	?> //disabled="true" 
	<?php //} ?> onclick='openPrint("print_outline2.php")' /></div>
-->
	<br>
	</td>
	</tr>
	</table>

	</body>
