<?php
session_start();
?>
<?php
$_SESSION['helpdesk_page']="trackTasks.php";
?>
<?php
require("db_page.php");
?>
<?php
/**
background-color: #0066cb;
color: #ffcc35;
*/
?>
<title>Monitor Current Client Requests</title>
	<?php 
	require("web_header.php");
	?>

<style type="text/css">
#cssTable {
background-color: #062e56;
color: white;

}
a:link {
text-decoration: none;
}
table {
	font: bold 14px "Trebuchet MS", Arial, sans-serif;
}	

#exception a{
text-decoration: none;
color: #ffffff;
}

#alterTable td{
background-color: #062e56;
color: white;
	
}

#alterTable th{
background-color: #062e56;
color: white;
	
}

#newTable td{
background-color:#66ceae;
color: #ffcc35;
	
}

#newTable th{
background-color: #0066cb;
color: #ffcc35;
	
}

	#menuh
	{
	padding-left: 0;
	width: 100%; 
	font-size: small;
	font: bold 14px "Trebuchet MS", Arial, sans-serif;
	float:left;
	
	}

	#menuh a
	{
	//This is the formatting of the links
	text-align: center;
	display:block;
	border: 1px solid #555;
	white-space:nowrap;
	margin:0;
	padding: 0.3em;
	}
	#menuh a:link, #menuh a:visited, #menuh a:active	/* menu at rest */
	{
	color: white;
	background-color: #00cc66;
	text-decoration:none;
	}
	

	#menuh a:hover	/* menu on mouse-over  */
	{
	color: black;
		background-color: #ed5214;
/**The color of the links */

	text-decoration:none;
	}	
	#menuh a.active {
	color: black;
		background-color: #ed5214;
	}
	
	#menuh a.top_parent, #menuh a.top_parent:hover  /* attaches down-arrow to all top-parents */
	{
	//background-image: url(http://62.0.5.133/sperling.com/examples/menuh/navdown_white.gif);
	background-position: right center;
	background-repeat: no-repeat;
	}
	#menuh a.parent, #menuh a.parent:hover 	/* attaches side-arrow to all parents */
	{
	background-position: right center;
	background-repeat: no-repeat;
	}
	#menuh ul
	{
	/**This places the overall menu to the straight line*/
	
	list-style:none;
	margin:0;
	padding:0;
	float:bottom;
	/* NOTE: For adjustable menu boxes you can comment out the above width rule.
	However, you will have to add padding in the "#menh a" rule so that the menu boxes
	will have space on either side of the text -- try it */
	}	
	
		#menuh ul ul
	{
	/**This places the submenu to minimize before hover*/
	
	position:absolute;
	z-index:500;
	top:0;
	left:100%;
	display:none;
	padding: 1em;
	margin:-1em 0 0 -1em;
	}
		#menuh ul ul ul
	{
	top:0;
	left:100%;
	}
	
	
	
		#menuh li
	{
	position:relative;
	min-height: 1px;		/* Sophie Dennis contribution for IE7 */
	vertical-align: bottom;		/* Sophie Dennis contribution for IE7 */
	}
	

	div#menuh li:hover
	{
	cursor:pointer;
	z-index:100;
	}

	div#menuh li:hover ul ul,
	div#menuh li li:hover ul ul,
	div#menuh li li li:hover ul ul,
	div#menuh  li li li li:hover ul ul
	{display:none;}

	div#menuh  li:hover ul,
	div#menuh  li li:hover ul,
	div#menuh  li li li:hover ul,
	div#menuh  li li li li:hover ul
	{display:block;}

</style>
<?php 
	$db=retrieveHelpdeskDb("primary");
	
	$yearLast=date("Y-m-d",strtotime(date("Y-m-d")."-2 months"));
	
	$sql="select *,(select count(*) from forward_admin where id=task.id) as forward_count,(select staffer from dispatch_staff where id=task.dispatch_staff) as dispatch_name from task where dispatch_time>'".$yearLast."' order by dispatch_time desc";
	$rs=$db->query($sql);
	$nm=$rs->num_rows;
	
?>
<body style="background-image:url('body background.jpg');">

<table  width="100%"  bgcolor="#FFFFFF" cellpadding="5px" bordercolor="#CCCCCC" style="border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px">

<tr>
	<?php 
	require("client_sidebar.php");
	//background-color:#66ceae; 
	?>
	<td width="85%" rowspan=2 valign="top"  style="background-color:#66ceae; border-bottom-style: solid; border-bottom-width: 1px; border-bottom-color:black;" bordercolor="#FF6600">

	<table id='alterTable' width=100% >
	<tr>
	<th colspan=7><h2>Client Requests List</h2></th>
	</tr>
	<tr>
		<th>Client Name</th>

		<th>Office Name</th>
		<th>Problem Details</th>
		<th>Reference Number</th>
		<th>Request Time</th>	
		<th>Client Status</th>	
		<th>Assigned Staff</th>
	</tr>
<?php
	for($i=0;$i<$nm;$i++){
		$row=$rs->fetch_assoc();
		
		$sql2="select * from division where division_code='".$row['division_id']."'";
		$rs2=$db->query($sql2);
		$row2=$rs2->fetch_assoc();
	
?>	
	<?php
	if($row['forward_count']>0){
		$tableStyle=" style='background-color:red;' ";
	}
	else {
		$tableStyle="";
	}
	if($row['dispatch_name']==""){
		$label="Not yet available";
	}
	else {
		$label=$row['dispatch_name'];
	
	}
	?>
	
	
	
	<tr>
		<td <?php echo $tableStyle; ?> ><?php echo $row['client_name']; ?></td>
		<td <?php echo $tableStyle; ?> ><?php echo $row2['division_name']; ?></td>
		<td <?php echo $tableStyle; ?> ><?php echo $row['problem_details']; ?></td>
		<td <?php echo $tableStyle; ?> ><?php echo $row['reference_number']; ?></td>
		<td <?php echo $tableStyle; ?> ><?php echo $row['dispatch_time']; ?></td>	
		<td <?php echo $tableStyle; ?> ><?php echo $row['status']; ?></td>	
		<td <?php echo $tableStyle; ?> ><?php echo $label; ?></td>	
	</tr>
<?php
	}
?>
	</table>
	</td>
</tr>
</table>	
</body>