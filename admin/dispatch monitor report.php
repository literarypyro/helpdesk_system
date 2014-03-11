<?php
session_start();
?>
<?php
require("form functions.php");
require("db_page.php");
?>
<?php
$_SESSION['helpdesk_page']="dispatch monitor report.php";

?>
<?php
//$db=new mysqli("localhost","root","","helpdesk_backup");
$db=retrieveHelpdeskDb();
$sql="select * from dispatch_staff inner join login  on dispatch_staff.id=login.username where dispatch_staff.id='".$_SESSION['username']."'";

$rs=$db->query($sql);
$userRow=$rs->fetch_assoc();
?>
<link rel="stylesheet" type="text/css" href="admin_staff.css" />
<script language='javascript'>
function selectOption(elementName,elementValue){
	var elm=document.getElementById(elementName);

	for(i=0;i<elm.options.length;i++){
		if(elm.options[i].value==elementValue){
			elm.options[i].selected=true;
		}
	}

}
</script>
	<title>Dispatcher Monitoring Report</title>
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
		}
		else {
			$staffClause=" dispatch_staffer='".$_POST['dispatch_staffer']."'";
			$m++;
			if($m>1){
				$conditionClause.=" and ".$staffClause;
			}
			else {
				$conditionClause.=$staffClause;
			
			}

		}

		if($_POST['date_filter']==""){
			$periodMonth=date("Y-m-d");
		
			$periodClause=" login_time like '".$periodMonth."%%'";
			$m++;
			
			if($m>1){
				$conditionClause.=" and ".$periodClause;
			}
			else {
				$conditionClause.=$periodClause;
			
			}
			$fromYear=date("Y");
			$toYear=$fromYear;
		}
		else {
			if($_POST['date_filter']=="dRange"){
				$periodMonthbeginning=$_POST['fromYear']."-".(date("m",strtotime(date("Y")."-".$_POST['fromMonth'])))."-".$_POST['fromDay'];
				$periodMonthend=$_POST['toYear']."-".(date("m",strtotime(date("Y")."-".$_POST['toMonth'])))."-".$_POST['toDay'];
				
				$periodClause=" login_time between '".$periodMonthbeginning." 00:00:00' and '".$periodMonthend." 23:59:59'";

				$fromYear=$_POST['fromYear'];
				$fromMonth=$_POST['fromMonth'];
				$fromDay=$_POST['fromDay'];
				$toYear=$_POST['toYear'];
				$toMonth=$_POST['toMonth'];
				$toDay=$_POST['toDay'];
			}
			else if($_POST['date_filter']=="daily"){
				$periodMonth=date("Y-m-d");

				$fromYear=date("Y");
				$fromMonth=date("m");
				$fromDay=date("d");
				$toYear=$fromYear;
				$toMonth=$fromMonth;
				$toDay=$fromDay;
				$periodClause=" login_time like '".$periodMonth."%%'";
			}
			else if($_POST['date_filter']=="weekly"){
				$periodMonthbeginning=date("Y-m-d",strtotime($_POST['fromYear']."-".(date("m",strtotime(date("Y")."-".$_POST['fromMonth'])))."-".$_POST['fromDay']));
				$periodMonthend=date("Y-m-d",strtotime($periodMonthbeginning." +6 days"));

				$periodClause=" login_time between '".$periodMonthbeginning." 00:00:00' and '".$periodMonthend." 23:59:59'";
				$fromYear=$_POST['fromYear'];
				$fromMonth=$_POST['fromMonth'];
				$fromDay=$_POST['fromDay'];
				$toYear=date('Y',strtotime($periodMonthend))*1;
				$toMonth=date('m',strtotime($periodMonthend))*1;
				$toDay=date('d',strtotime($periodMonthend))*1;

			}
			else if($_POST['date_filter']=="monthly"){
				$periodMonth=date("Y")."-".(date("m",strtotime(date("Y")."-".$_POST['fromMonth'])));
				$periodClause=" login_time like '".$periodMonth."%%'";
				
				$fromYear=$_POST['fromYear'];
				$fromMonth=$_POST['fromMonth'];
				$fromDay=$_POST['fromDay'];
				$toYear=$_POST['toYear'];
				$toMonth=$fromMonth;
				$toDay=30;
			}

			else {
				$periodMonth=date("Y");
				$periodClause=" login_time like '".$periodMonth."%%'";
				
				
				$fromYear=$_POST['fromYear'];
				$fromMonth=1;
				$fromDay=1;
				$toYear=$fromYear;
				$toMonth=12;
				$toDay=31;
				
			}

			$m++;

			
			if($m>1){
				$conditionClause.=" and ".$periodClause;
			}
			else {
				$conditionClause.=$periodClause;
			
			}

		}
//		$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();
		$new_sql="select * from dispatch_track ".$conditionClause." order by login_time desc";
		?>
	<script language="javascript">
	
	function openPrint(url){
		window.open(url);
	
	}
	</script>
	<body style="background-image:url('body background.jpg');">
	<?php 
	require("web_header.php");
	?>
	<!--Heading Table-->
	<table  width="100%"  bgcolor="#FFFFFF" cellpadding="5px" bordercolor="#CCCCCC" style="border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px">
<tr>
	<th colspan=2 class='subheader'	align=right>Administrator: <font color=black><?php echo $userRow['staffer']; ?></font></th>
</tr>

<tr>
	<?php 
	require("admin_sidebar.php");
	//background-color:#66ceae; 
	?>
	<td width="85%" rowspan=2 valign="top"  style="background-color:hsl(225,80%,70%); border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color:black;" bordercolor="#FF6600">

	<form action='dispatch monitor report.php' method=post>

	<?php 

	$headingTable="
	<table align=center width=100%>
	<tr><th colspan=2><h2>DISPATCH UPDATE REPORT</h2></th>
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
		//$db=new mysqli("localhost","root","","helpdesk_backup");
		$db=retrieveHelpdeskDb();

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
	<th colspan=4><h2>Dispatcher Update List</h2></th>
	</tr>

	<tr>
		<th>Dispatch Staff</th>
		<th>Login Time</th>

		<th>Location</th>
		<th>Client Request Id</th>
<!--
		<th>Status of Request</th>	
-->
	</tr>	
	<?php
		for($i=0;$i<$nm;$i++){
		$row=$new_rs->fetch_assoc();
		
		
		$sql2="select * from dispatch_staff where id='".$row['dispatch_staffer']."'";
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
		<td><?php echo trim($row2['staffer']); ?></td>
		<td><?php echo date("F d, Y h:ia",strtotime($row['login_time'])); ?></td>
		<td><?php echo $row['location']; ?></td>
		<td><?php echo $row3['reference_number']; ?></td>

	</tr>
	<?php
		}
	?>
	</table>
	
	<div align=center><input type=button value='Prepare Print Out' <?php if($count==0){ ?> disabled="true" <?php } ?> onclick='openPrint("print_outline2.php")' /></div>
	<br>
	</td>
	</tr>
	</table>
	
	<?php
	echo "
	<script language='javascript'>
	selectOption('fromYear','".$fromYear."');
	selectOption('fromMonth','".$fromMonth."');
	selectOption('fromDay','".$fromDay."');
	selectOption('toYear','".$toYear."');
	selectOption('toMonth','".$toMonth."');
	selectOption('toDay','".$toDay."');
	</script>
	";
	?>
	</body>
