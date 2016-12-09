
	<?php
	include_once'connect.php';
	include_once'db.php';
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>i-BILL</title>
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
				</script>
</head>

<body><div align='center'  class='container'>
<h1 class='header' style='font-family: monospace'>i-BILL<small>[lite]</small></h1>
<strong><span style='float: left'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $date_present;?></span></strong>
	<strong><span id='tick2' style='float:right'></span></strong>
<div style='text-align: center;'>
	<a class='navigation' href='index.php'>Make bill</a>
	<a class='navigation' href='search.php'>Search bill</a>
        <a class='navigation' href='populate.php'>Enter product</a>
	<a class='navigation' href='sales.php'>Sales</a>
	<a class='navigation' href='payment.php'>Payment</a>
	<a class='navigation'  style='color: #FFF;background-color:#0080ff;' href='histroy.php'>Histroy</a>
	<a class='navigation' href='buyer.php'>Buyer</a>
	
</div>	
<br>
	<div class='buyerflex'>
<form method='post'>
	
	<strong>BUYER</strong>
	<select onchange="this.form.submit();" name='buyer' class='box'>
			<option value=''></option>
		<?php
		$r=$dbclass->select_normal('DISTINCT buyers_name','bill_base','ORDER BY buyers_name',$dbc);
		while($row=mysqli_fetch_assoc($r)){
			if(!is_null($row['buyers_name'])&&!empty($row['buyers_name'])){
			echo"<option value='".$row['buyers_name']."'>".$row['buyers_name']."</option>";
			}
		}
		?>
			
	</select>
</form>
	</div>
	<div class='salesflex' align='center' style='margin: 5px;text-align: center;'>
		<div style='background-color: #0080ff;font-size: 20px;'><strong>HISTROY</strong></div>
<?php
if(!isset($_POST['buyer'])){
	echo "<div style='opacity:50%;margin-top:90px;'><q><i> Select BUYER to see the histroy</i></q> </div>";
}
if(isset($_POST['buyer'])){
	$buyer=$_POST['buyer'];
	?>
	
		
	<table cellspacing='13' algin='center' width='100%'>
		<th>Invoice no</th>
		<th>Buyer</th>
		<th>Date</th>
		<th>Time</th>
		
		<th>Amount paid</th>
		<th>Due left</th>
		<th>Due days</th>
	<?php

 if($_POST['buyer']!=''&&!is_null($_POST['buyer'])){
	$r=$dbclass->select('*','bill_histroy',"buyers_name='$buyer' ORDER BY idbill_histroy DESC LIMIT 0,10",$dbc);
	while($row=mysqli_fetch_assoc($r)){$id=$row['bill_no'];
		?>
		<tr>
			<td><?php echo $row['bill_no'];?></td>
			<td><?php echo $row['buyers_name'];?></td>
			<td><?php echo $row['date'];?></td>
			<td><?php echo $row['time'];?></td>
			<td><?php echo $row['amount'];?></td>
			<td><?php echo $row['due_left'];?></td>
			<?php
		//calculating the due days
		if($row['due_left']){
		$_1=date_create($date_present);
		$t=$dbclass->select('date','bill_base',"idbill_base='$id'",$dbc);
		while($y=mysqli_fetch_assoc($t)){
			$_2=$y['date'];
		}
                $_2=date_create($_2);
                $diff=date_diff($_1,$_2);
                echo '<td>'.$diff->format("%a days").'</td>';
		}
		else{
			echo '<td>PAID</td>';
		}
		?>
			
		</tr>
		
		
		<?php
	}
 }
	echo'</table>';
}

?>
</div>
<br>
<div class='footer'>Created and owned by</div><img src='icoders.jpg' width='150' height='37'>
</body>
</html>
