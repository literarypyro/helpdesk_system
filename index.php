<?php
session_start();
?>
<?php
//$pc_name=gethostbyaddr($_SERVER['REMOTE_ADDR']);

$urlAdd="";
if(isset($_GET['bP'])){
	$pc_name="chix";
	$urlAdd="?bP=".$_GET['bP'];
	
}

if($pc_name=="chix"){
	require("helpdesk/db_page.php");
	if(isset($_POST['username'])){
//		$db=localOnlyDb();
		$db=new mysqli("localhost","root","","helpdesk_system");
		$sql="select * from login where username='".trim($_POST['username'])."' and password='".trim($_POST['password'])."'";

		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		if($nm>0){
			$row=$rs->fetch_assoc();
			$usertype=$row['type'];
			$loginSQL="insert into log_history(username, time, action) values ('".$_POST['username']."','".date("Y-m-d H:i:s")."','login')";
			$loginrs=$db->query($loginSQL);
			$loginnm=$loginrs->num_rows;
			$_SESSION['username']=$_POST['username'];
			
			if($row['type']=="Administrator"){
				header("Location: admin/taskmonitor.php");
			}
			else {
				header("Location: helpdesk/taskmonitor.php");
			}
		}
		else {
			header("Location: index.php".$urlAdd);	
		
		}
	
	
	}
	else {
?>
<style type="text/css">
#cssTable {
background-color: hsl(225,40%,30%);
color: white;


}
a:link {
text-decoration: none;
}

#exception a{
text-decoration: none;
color: #ffffff;
}

#alterTable td{
background-color: #062e56;
color: white;
	
}
table {
	font: bold 14px "Trebuchet MS", Arial, sans-serif;
}	


#alterTable th{
background-color: #0066cb;
color: #ffcc35;
	}
	
	#menuh
	{
	padding-left: 0;
	width: 100%; 
	font-size: small;
		font: bold 14px "Trebuchet MS", Arial, sans-serif;

	//	font: Times New Roman;
	//width:50%;
//BACKGROUND-COLOR: #4b5ed7;

//background-color: #172caf;
	float:left;
//	margin:
	//margin-top: 1em;
	
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
	color: #bd2031;
	background-color: #00cc66;
//	background-color: royalblue;
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
//	background-image: url(http://62.0.5.133/sperling.com/examples/menuh/nav_white.gif);
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
//	width:9em;	/* width of all menu boxes */
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
<!--
	<div align=center><img src="helpdesk/mrt3.jpg" style="width:100%; height:350;" /></div>
	-->
	<div align=center width=100%>
	<table  class='exception'  style=''>
<tr>
<td >
<!--
<img src='mrt-logo.png' style='width:100%;height:100%;' />
-->
<img src='mrt-logo.png' style='width:130px;height:80px;' />

</td><td valign=center ><font style='font-size:25px;'><h2><b>Online Helpdesk System</b></h2></font>
</td>
</tr>
</table>
</div>
<br>
<form enctype="multipart/form-data" action='index.php<?php echo $urlAdd; ?>' method='post'>
<table id='cssTable' align=center style='border: 1px solid gray'>
<tr><th colspan=2>Log-In Here:</th></tr>
<tr>
	<td>Enter Username:</td>
	<td>
		<select name='username'>
		<option>&nbsp;</option>
		<?php
		$db=new mysqli("localhost","root","","helpdesk_system");
		$sql="select * from login inner join dispatch_staff on login.username=dispatch_staff.id";
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		for($i=0;$i<$nm;$i++){
			$row=$rs->fetch_assoc();
		?>
			<option value='<?php echo $row['username']; ?>'><?php echo $row['staffer']; ?></option>
		<?php
		
		}
		
		?>
		</select>
	</td>
</tr>
<tr>
	<td>Enter Password:</td>
	<td><input type='password' name='password' size=40 /></td>
</tr>
<tr>
	<td colspan=2 align=center><input type=submit value='Submit' /></td>
</tr>
</table>
</form>

<?php			
	}	
}
else {
?>
<style type='text/css'>
table {
	font: bold 14px "Trebuchet MS", Arial, sans-serif;
}	

</style>

<script language="javascript">
function startSequence(){
showDialog('Welcome to Helpdesk','Good day! Allow us to assist you!','prompt');

//hideDialog();
//  location.href="client/index.php";
}
</script>
<body onload="startSequence();" >
<link rel="stylesheet" type="text/css" href="dialog_box.css" />
<script type="text/javascript" src="dialog_box.js"></script>

<div id="content">
<!--
	<div align=center><img src="helpdesk/mrt3.jpg" style="width:100%; height:350;" /></div>

	</div>
-->
<div align=center>
<table class='exception'  style=''>
<tr>
<td >
<!--
<img src='mrt-logo.png' style='width:100%;height:100%;' />
-->
<img src='mrt-logo.png' style='width:130px;height:80px;' />

</td><td valign=center><font style='font-size:25px;'><h2><b>Online Helpdesk System</b></h2></font>
</td>
</tr>
</table>
</div>
	</body>
<?php

//	echo "Thank you for using HDC Online System! Please come again!";
//	echo "If the page does not redirect to web page, click <a href='client/index.php'>here</a><br>";
//	echo "Or <a href='index.php?bP=1a8o990dDm13d3lC35'>Not a Client/Customer</a>";

/**
	var answer=confirm("Good day! May we assist you?\n Press OK to proceed.");
	if(answer==true){
		location.href="client/index.php";
	}
	else {
//		location.href="index.php?bP=1a8o990dDm13d3lC35";
	}
*/

}

?>
