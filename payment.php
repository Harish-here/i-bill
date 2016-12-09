<?php
include_once'connect.php';
include_once'db.php';
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
	<a class='navigation'  style='color: #FFF;background-color:#0080ff;' href='payment.php'>Payment</a>
	<a class='navigation' href='histroy.php'>Histroy</a>
		<a class='navigation' href='buyer.php'>Buyer</a>
</div>
<div class='dueflex'>
	
		<div style='background-color: #0080ff;width: auto;'>
		<strong>	
			Buyer's Due Amount
		</strong>
		</div>
		
			<div style='overflow-y: auto;height: 340px;width: 100%;'>
		<table align='center' cellspacing='13' >
			<tr>
				<th>&nbsp;&nbsp;Buyer&nbsp;&nbsp;</th>
				<th>&nbsp;&nbsp;Balance&nbsp;&nbsp;</th>
			</tr>
		<?php
		$r=$dbclass->select_normal('DISTINCT buyers_name','bill_base',"ORDER BY balance DESC",$dbc);//getting the unique buyer name
		while($t=mysqli_fetch_assoc($r)){
			$buyer=$t['buyers_name'];
			$balance=$dbclass->select('SUM(balance) as balance','bill_base',"buyers_name='$buyer'",$dbc);//getting their balance
			while($e=mysqli_fetch_assoc($balance)){
				$bal=$e['balance'];
			}
			if(!is_null($buyer)&&$bal!=0)
			echo"<tr><td id='center'><b><a style='color:#000' href='payment.php?buyer=".$buyer."'>".$buyer."</a></b></td><td id='center'><b>₹ ".$bal.'</b></td></tr>';
			
		}
		
		?>
		</table></div>
	</div>
<br>
	<div class='buyerflex'>
	<form method='post'>
		
	<b>BUYER</b>
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
<br>
	<div class='salesflex' style='width: 75%;'>
		<div style='background-color:#0080ff;width: auto;padding: 0px;font-size: 20px;'><strong>PAYMENT</strong></div>
<?php
if(!isset($_POST['buyer'])&&!isset($_GET['buyer'])){
	echo "<div style='opacity:50%;margin-top:90px;'><q> <i>Select BUYER to See the details</i></q> </div>";
}
if(isset($_POST['add'])){
	$buyername=$_POST['buyername'];
	$amount=(is_numeric($_POST['paid']))?$_POST['paid']:NULL;
	$dategiven=$_POST['date']." ".$_POST['month']." ".$_POST['year'];
	// to pay the balance alogrithm
	$t=$dbclass->select('*','bill_base',"buyers_name='$buyername' AND balance>0 ORDER BY idbill_base ASC",$dbc);
	while($s=mysqli_fetch_assoc($t)){
		$id=$s['idbill_base'];
		$bal=$s['balance'];
		$remain=$s['balance']-$amount;
		if($remain>0&&$amount!=0){
			$dbclass->update('bill_base',"balance='$remain'","idbill_base='$id'",$dbc);
			$dbclass->insert('bill_histroy',"VALUES(NULL,'$id','$dategiven','$time_present','$buyername','$amount','$remain')",$dbc);
		}
		elseif($remain<0&&$amount!=0){
			$dbclass->update('bill_base',"balance=0","idbill_base='$id'",$dbc);
			$amount=$amount-$s['balance'];
			$dbclass->insert('bill_histroy',"VALUES(NULL,'$id','$dategiven','$time_present','$buyername','$amount',0)",$dbc);
		}
	}
	
	
	
}
if(isset($_POST['buyer'])||isset($_POST['add'])||isset($_GET['buyer'])){
	if(isset($_POST['buyer'])){
		$buyer=(isset($_POST['buyer'])&&($_POST['buyer']!=''))?$_POST['buyer']:NULL;
	}
	elseif(isset($_POST['add'])){
		$buyer=$_POST['buyername'];
	}elseif(isset($_GET['buyer'])){
		$buyer=$_GET['buyer'];
	}
	
	if(!is_null($buyer)){
		$conditions=" buyers_name='$buyer' ORDER BY idbill_base DESC";
	}
	$select="SUM(balance) AS balance,buyers_name,SUM(total) AS total";
	$r=$dbclass->select($select,'bill_base',$conditions,$dbc);
	while($row=mysqli_fetch_assoc($r)){
		?>
		<table align='center' cellspacing='8' style='background-color: #8080ff;border: solid 1px #fff;border-radius: 10px;font-weight:bolder;'><tr>
			<th>BUYER</th>
			<th>&nbsp;&nbsp;&nbsp;&nbsp;TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</th>
			<th>&nbsp;&nbsp;&nbsp;&nbsp;DUE LEFT&nbsp;&nbsp;&nbsp;&nbsp;</th>
			<th>AMOUNT PAID</th>
			<th>DATE</th>
			<th>ADD</th>
			<th>DETAILS</th>
			</tr>
			<tr>
				<td>
					<?php echo $row['buyers_name'];?>
				</td>
				<td><?php echo '₹ '.$row['total'];?></td>
				<td>
					<?php echo '₹ '.$row['balance'];?>
				</td>
				<td><form method='post'>
					<input style='padding: 7px' class='box' type='number' name='paid' placeholder='creadit balance' />
					</td>
				<td>
					<select class='box' name='date'>
						<option value='01'>01</option>
						<option value='02'>02</option>
						<option value='03'>03</option>
						<option value='04'>04</option>
						<option value='05'>05</option>
						<option value='06'>06</option>
						<option value='07'>07</option>
						<option value='08'>08</option>
						<option value='09'>09</option>
						
					<?php
					for($q=10;$q<=31;$q++){
						echo "<option value='".$q."'>".$q."</option>";	
					}
					?>
						
					</select>
					<select class='box' name='month'>
						<option value='Jan'>Jan</option>
						<option value='Feb'>Feb</option>
						<option value='Mar'>Mar</option>
						<option value='Apr'>Apr</option>
						<option value='May'>May</option>
						<option value='Jun'>Jun</option>
						<option value='Jul'>Jul</option>
						<option value='Aug'>Aug</option>
						<option value='Sep'>Sep</option>
						<option value='Oct'>Oct</option>
						<option value='Nov'>Nov</option>
						<option value='Dec'>Dec</option>
					</select>
					<select class='box' name='year'>
						<option value='14'>14</option>
					</select>
				</td>
				<td>
					<input id='btn' class='box' type='submit' name='add' value='+'>
						<input type='hidden' name='buyername' value='<?php echo $row['buyers_name'];?>'>
						</td>
							<td>
						<a href='payment.php?buyer=<?php echo $row['buyers_name'];?>'>view bill details</a>
						</form>
				</td>
			</tr>
			<span style='font-style: italic;size: 20px;'><b><u>DUE PAY</u></b></span>
		</table>
		
		<br>
		<?php
		
	}
}
if(isset($_GET['buyer'])&&!isset($_POST['buyer'])){
	$name=$_GET['buyer'];
	$r=$dbclass->select('*','bill_base',"buyers_name='$name' ORDER BY idbill_base DESC LIMIT 0,8",$dbc);
	?><span style='font-style: italic;size: 20px;'><b>Last 8 bills of <?php echo $name;?></b></span>
	<table align="center" cellspacing="8" style="background-color: #8080ff;border-radius: 10px;font-weight:bolder;" >
		<th>Bill no</th>
		<th>&nbsp;&nbsp;&nbsp;Date&nbsp;&nbsp;&nbsp;</th>
		<th>&nbsp;&nbsp;&nbsp;Time&nbsp;&nbsp;&nbsp;</th>
		<th>Bill Amount</th>
		<th>&nbsp;&nbsp;Balance&nbsp;&nbsp;</th>
		<th>DUE days</th>
		<th>Details</th>
	<?php 
	while($row=mysqli_fetch_assoc($r)){
		?>
		<tr>
			<td><?php echo $row['idbill_base']; ?></td>
		<td><?php echo $row['date']; ?></td>
		<td><?php echo $row['time']; ?></td>
		<td><?php echo '₹ '.$row['total'];?></td>
		<td><?php echo '₹ '.$row['balance'];?></td>
		<?php
		//calculating the due days
		if($row['balance']){
		$_1=date_create($date_present);
                $_2=date_create($row['date']);
                $diff=date_diff($_1,$_2);
                echo '<td>'.$diff->format("%a days").'</td>';
		}
		else{
			echo '<td>PAID</td>';
		}
		?>
		<td><a href='search.php?billid=<?php echo $row['idbill_base'];?>'>&nbsp;&nbsp;View</a></td>
		</tr>
		
		<?php
	}
	echo'</table>';
}

?>
		</div>
<div class='footer' align='center'>Created and owned by<br><img align='center' src='icoders.jpg' width='150' height='37'></div>
</body>
</html>
