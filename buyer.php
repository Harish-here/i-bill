<?php
include_once'connect.php';
include_once'db.php';

if(isset($_POST['submit'])){
	//adding the new buyer's details
	$name=(!is_null($_POST['buyers_name'])&&!empty($_POST['buyers_name']))?$dbclass->clear_string($_POST['buyers_name'],$dbc):NULL;
	$address=(!is_null($_POST['buyers_address'])&&!empty($_POST['buyers_address']))?$dbclass->clear_string($_POST['buyers_address'],$dbc):NULL;
	$no=(!is_null($_POST['buyers_number'])&&!empty($_POST['buyers_number']))?$dbclass->clear_string($_POST['buyers_number'],$dbc):NULL;
	$mail=(!is_null($_POST['buyers_email'])&&!empty($_POST['buyers_email']))?$dbclass->clear_string($_POST['buyers_email'],$dbc):NULL;
	$check=$dbclass->select('*','buyer_base',"buyers_name='$name'",$dbc);
	if(mysqli_num_rows($check)==0){
	if(!is_null($name)&&!is_null($address)&&!is_null($no)&&!is_null($mail)){
		$dbclass->insert('buyer_base',"VALUES(NULL,'$name','$address','$no','$mail')",$dbc);
		$msg="<b>".$name." and his details added</b>";
	}
	}else{
		$msg='<b>'.$name.' is Already present</b>';
	}
}

//printing all the names
if(isset($_GET['print'])){
		echo"<script type='text/javascript'>
		window.open('/contactpdf.php');
		</script>";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>i-BILL</title>
	<meta charset='uft-8'>
	<link href='stylesheet.css' type='text/css' rel='stylesheet'>
	<script>
				function show2(){
				if (!document.all&&!document.getElementById)
				return
				thelement=document.getElementById? document.getElementById("tick2"): document.all.tick2
				var Digital=new Date()
				var hours=Digital.getHours()
				var minutes=Digital.getMinutes()
				var seconds=Digital.getSeconds()
				var dn="PM"
				if (hours<12)
				dn="AM"
				if (hours>12)
				hours=hours-12
				if (hours==0)
				hours=12
				if (minutes<=9)
				minutes="0"+minutes
				if (seconds<=9)
				seconds="0"+seconds
				var ctime=hours+":"+minutes+":"+seconds+" "+dn
				thelement.innerHTML=ctime
				setTimeout("show2()",1000)
				}
				window.onload=show2
				//-->
				function ff() {
	document.getElementById('here').focus();
	}
	
</script>
	<style>
		td{
			padding: 10px;
		}
	</style>
</head>
<body><div class='container'>
<h1 class='header' style='font-family: monospace'>i-BILL<small>[lite]</small></h1>
<strong><span style='float: left'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $date_present;?></span></strong>
<strong><span id='tick2' style='float:right'></span></strong>
<div style='text-align: center;'>
	<a class='navigation'  href='index.php'>Make bill</a>
	<a class='navigation' href='search.php'>Search bill</a>
        <a class='navigation' href='populate.php'>Enter product</a>
	<a class='navigation' href='sales.php'>Sales</a>
	<a class='navigation' href='payment.php'>Payment</a>
	<a class='navigation' href='histroy.php'>Histroy</a>
	<a class='navigation' style='color: #FFF;background-color:#0080ff; ' href='buyer.php'>Buyer</a>
	
</div>
<br><br>
<div style='float: right'>
<div class='buyerflex'>
	<form method='get'>
	<b>VIEW DETAILS</b>
	<select class='box' name='buyer' onchange="this.form.submit();">
		<option> </option>
		<option value='ALL'>All</option>
		<?php
		$t=$dbclass->select_normal('buyers_name as buyer','buyer_base',"ORDER BY buyers_name ASC",$dbc);
		while($e=mysqli_fetch_assoc($t)){
			echo"<option value='".$e['buyer']."'>".$e['buyer']."</option>";
		}
		?>
		
	</select>
</form>	
</div>
<div class='buyerflex' style='margin-top: 10px;'>
	<ul>
	<li><b>NEW BUYER</b></li>
	
	<form method='post'>
		<li><input type='text' name='buyers_name' placeholder='Buyer name' class='box' required='required'>
		</li>
		<li><textarea name='buyers_address' style='font-family:sans-serif;' placeholder='Buyers Address' class='box' required='required'></textarea>
		</li>
			<li><input type='number' name='buyers_number' placeholder='Phone number' class='box' required='required'>
				</li><li>	<input type='email' name='buyers_email' placeholder='Email' class='box' required='required'></li>	
						<li><input type='submit' name='submit' value='ADD' id='btn' /></li>
	</form>
	</ul>
</div>

</div>
<div class='salesflex' style='width: 65%;'>
	<a href="?print" style='float: right;color: #000;'><b>Print all contact</b></a>
	<div style='background-color:#0080ff;width: auto;padding: 3px;font-size: 20px;'><strong>Buyer</strong></div>
	<?php
	if(isset($msg)){
		echo $msg;
	}
	
	
if(isset($_GET['buyer'])){
	
	//showing the details
	$name=$_GET['buyer'];
	if($name !='ALL'){
	$r=$dbclass->select('*','buyer_base',"buyers_name='$name'",$dbc);
	}
	elseif($name=='ALL'){
	$r=$dbclass->select_normal('*','buyer_base'," ORDER BY buyers_name ASC",$dbc);
	}
	?>
	<table border='1' cellpadding='10'>
		<th>BUYER</th>
		<th>ADDRESS</th>
		<th>MOBILE NO</th>
		<th>EMAIL</th>
	<?php
	//extracting the data from the result
	while($y=mysqli_fetch_assoc($r)){
	echo"
	<tr>
	<td>".$y['buyers_name']."</td>
	<td>".$y['buyers_address']."</td>
	<td>".$y['buyers_no']."</td>
	<td>".$y['buyers_email']."</td>
	</tr>
	";	
	}
	echo'</table>';
}

	
	?>
</div>



	<div class='footer' align='center' >Created and owned by<br><img src='icoders.jpg' width='150' height='37'></div>
</body>
</html>
