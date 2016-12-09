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
	<title>i-BILL</title>
	<link href='stylesheet.css' type='text/css' rel='stylesheet'>
</head>
<body><div align='center'  class='container'>
<h1 class='header' style='font-family: monospace'>i-BILL<small>[lite]</small></h1>
<div style='text-align: center;'>
	<a class='navigation' href='index.php'>Make bill</a>
	<a class='navigation' href='search.php'>Search bill</a>
        <a class='navigation' href='populate.php'>Enter product</a>
	<a class='navigation' style='color: #FFF;background-color:#0080ff;' href='sales.php'>Sales</a>
	<a class='navigation' href='payment.php'>Payment</a>
	<a class='navigation' href='histroy.php'>Histroy</a>
		<a class='navigation' href='buyer.php'>Buyer</a>
</div>
<br>
	<div class='dueflex' >
				<div style='background-color: #0080ff;width: auto;'>
		<strong>	
			Prevoius Day Sales
		</strong>
		</div>
			<?php
			//showing the previous bills
			$t=$dbclass->select_normal('DISTINCT date','bill_base','ORDER BY idbill_base DESC LIMIT 0,15',$dbc);
			?><div style='overflow-y: auto;height: auto;width: 100%;'>
			<table style='width: 15%;font-size: 11px;padding: 0px;' align='center' cellspacing='9' cellpadding='7'>
					<tr>
						<th>&nbsp;&nbsp;Date&nbsp;&nbsp;	</th>
						<th>&nbsp;&nbsp;&nbsp;Sales&nbsp;&nbsp;&nbsp;</th>
						<th>Details</th>
					</tr>
			<?php
			
			while($row=mysqli_fetch_assoc($t)){
				$date=$row['date'];//getting the date
				$y=$dbclass->select('SUM(total) AS total','bill_base',"date='$date'",$dbc);
				while($u=mysqli_fetch_assoc($y)){
					$total=$u['total'];//getting the toal amount inthat date;
				}
				?>
				
					<tr>
						<span style='font-size: 18px;'><td><b><?php echo $date;?></b></td>
						<td><?php echo '₹ '.$total;?></td>
						<td><a href='sales.php?date=<?php echo $date;?>'>View</a></td>
						</span>
					</tr>
				
				<?php
			}
			
			?></table></div>
	</div>
	<div style='width:450px;background-color: #808080;border: solid 1px #000;border-radius:5px;' align='center'>
<form method='post'>
	<table align='center'>
		<tr>
			
			<td>From<select name='from' class='box'>
				<?php
				$r=$dbclass->select_normal('DISTINCT date','bill_base','ORDER BY idbill_base DESC ',$dbc);
				while($row=mysqli_fetch_assoc($r)){
					echo"<option vlaue='".$row['date']."'>".$row['date']."</option>";
				}
				?>
			</select>
			<td>To<select name='to' class='box'>
				<?php
				$r=$dbclass->select_normal('DISTINCT date','bill_base','ORDER BY idbill_base DESC ',$dbc);
				while($row=mysqli_fetch_assoc($r)){
					echo"<option vlaue='".$row['date']."'>".$row['date']."</option>";
				}
				?>
			</select>
		
			<td><strong>Buyer</strong><select name='buyer' class='box'>
			<option value=''></option>
	
		<?php
		$r=$dbclass->select_normal('DISTINCT buyers_name','bill_base','ORDER BY buyers_name',$dbc);
		while($row=mysqli_fetch_assoc($r)){
			if(!is_null($row['buyers_name'])&&!empty($row['buyers_name'])){
			echo"<option value='".$row['buyers_name']."'>".$row['buyers_name']."</option>";
			}
		}
		?>
	</select></td>
			
		
		<td>
			</td>
			<td><input id='btn' type='submit' name='show' value='show' class='box'></td>
		
		</tr>
	</table>
</form>
</div>
	<div class='salesflex'>
			<div style='background-color:#0080ff;width: auto;padding: 3px;font-size: 20px;'><strong>sales</strong></div>
<?php
if(!isset($_POST['show'])&&!isset($_GET['date'])){
	echo "<div style='opacity:50%;margin-top:90px;'><q><i> Select date And CLICK show</i></q> </div>";
}


if(isset($_POST['show'])||isset($_GET['date'])){
	if(isset($_POST['show'])){
//intilization of all parameters

$from=(isset($_POST['from']))?$_POST['from']:NULL;
$to=(isset($_POST['to']))?$_POST['to']:NULL;
$buyer=(isset($_POST['buyer'])&&($_POST['buyer']!=''))?$_POST['buyer']:NULL;
$day=(isset($_POST['day'])&&is_numeric($_POST['day']))?$_POST['day']:NULL;
$re=$dbclass->select('*','bill_base',"date='$from' ORDER BY idbill_base ASC LIMIT 0,1 ",$dbc);
while($row=mysqli_fetch_assoc($re)){
	$bill1=$row['idbill_base'];
}
$rs=$dbclass->select('*','bill_base',"date='$to' ORDER BY idbill_base ASC LIMIT 0,1",$dbc);
while($row=mysqli_fetch_assoc($rs)){
	$bill2=$row['idbill_base'];
}
//checking the right conditions
if(!is_null($from)&&!is_null($buyer)&&!is_null($to)){
	$conditions="idbill_base>='$bill1' AND idbill_base<='$bill2' AND buyers_name='$buyer' ORDER BY idbill_base DESC";	
}elseif(!is_null($from)&&!is_null($to)){
	$conditions="idbill_base>='$bill1' AND idbill_base<='$bill2' ORDER BY idbill_base DESC";
	
}

}
elseif(isset($_GET['date'])){
	$date=$_GET['date'];
	$conditions="date='$date' ORDER BY idbill_base DESC ";
}
 $result=$dbclass->select('*','bill_base',$conditions,$dbc);
?>

			<table cellspacing='13' align='center' style='font-size: 15px;'>
				<th>BILL No</th>
				<th>&nbsp;&nbsp;&nbsp;&nbsp;BUYER&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<th>DATE</th>
				<th>ITEMS CART </th>
				<th>&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;</th>
				<th>DUE days</th>
				<th>Details</th>
<?php
$total=0;
	while($row=mysqli_fetch_assoc($result)){
		if(!is_null($row['buyers_name'])){
		?>
		
				<tr>
					<td>
						<?php echo '&nbsp;'.$row['idbill_base'];?>
					</td>
					<td>
					<b>	<?php echo '&nbsp;'.$row['buyers_name'];?></b>
					</td>
					<td>
						<?php echo '&nbsp;'.$row['date'];?>
					</td>
					
					<td>
						<?php echo '&nbsp;'.$row['items_cart']; ?>
					</td>
					
					<?php
		//calculating the due days + checking the balance
		if($row['balance']){
		$_1=date_create($date_present);
                $_2=date_create($row['date']);
                $diff=date_diff($_1,$_2);
                $due=$diff->format("%a days");
		}else{
			$due='PAID';
		}
		?>
					<td>
					<b>	<?php echo '₹&nbsp;'.$row['total'].'</b>';
						echo "&nbsp;&nbsp;&nbsp;</td><td>".$due."</td><td><a href='search.php?billid=".$row['idbill_base']."'>view</a>"?>
						
					</td>
				</tr>			
		
		<?php
		}
		$total=$row['total']+$total;
	}
	echo"<br><tr><strong>Total sale: ₹ ".$total."</strong></tr>";
	if(!isset($date)||isset($_POST['show']))
	echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>FROM:</b>&nbsp;'.$from."&nbsp;&nbsp;&nbsp;&nbsp;  <b>TO:</b>&nbsp;".$to." ";
	elseif(!isset($from)&&!isset($to))
	echo "&nbsp;&nbsp;&nbsp;<b>On: </b>".$date." ";
}
?>
</table>
			
		</div>
<br>
	<div class='footer' align='center'>Created and owned by<br><img align='center' src='icoders.jpg' width='150' height='37'></div>
</body>
</html>
