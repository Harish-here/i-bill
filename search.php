<!DOCTYPE>
	<?php
	include_once'connect.php';
	include_once'db.php';
	
	//returning the goods
	if(isset($_POST['reduce'])){
		$id=$_POST['billno'];
		$r=$dbclass->select('*','product_base',"bill_no='$id'",$dbc);
		while($row=mysqli_fetch_assoc($r)){
			$pdt=$row['product'];
			$pcs=$row['pcs'];
			$mtr=$row['meter'];
			$rs=$row['price'];
			if(isset($_POST[$pdt])&&is_numeric($_POST[$pdt])){
				if($pcs>=$_POST[$pdt]){
					$pcs=$_POST[$pdt];
					if($mtr!='-'&&$mtr!=0){
					$tlt=$rs*-1*$_POST[$pdt]*$mtr;
					}else{
					$tlt=$rs*-1*$_POST[$pdt];	
					}
					$pdt=$pdt.'(RG)';
				
				$dbclass->insert('product_base',"VALUES(NULL,'$id','$pdt','$pcs','$mtr','$rs','$tlt')",$dbc);	
			}
			}
		}
		$y=$dbclass->select('SUM(total) AS total','product_base',"bill_no='$id'",$dbc);
		while($l=mysqli_fetch_assoc($y))
		$set=$l['total'];
		$dbclass->update('bill_base',"total='$set'","idbill_base='$id'",$dbc);
		
	}
	
	
	
	
	
	if((isset($_POST['search'])&&!empty($_POST['billno']))||isset($_GET['billid'])){
		$billno=(isset($_POST['billno']))?$dbclass->clear_string($_POST['billno'],$dbc):$_GET['billid'];
		$res=$dbclass->select('*','bill_base',"idbill_base='$billno'",$dbc);
		if(mysqli_num_rows($res)!=0){
			while($row=mysqli_fetch_assoc($res)){
			$searchbillno=$row['idbill_base'];
			$itemscart=$row['items_cart'];
			$total=$row['total'];
			$billdate=$row['date'];
			$billtime=$row['time'];
			$billbuyer=$row['buyers_name'];
		}
		}
		else{
		$msg="<i>NO BILL FOUND</i>";	
		}
		
	}
if(isset($_GET['id'])){
$id=$_GET['id'];
echo"<script type='text/javascript'>
		window.open('/	email.php?id=".$id."&&email');
		</script>";}	
	
	
	
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
<script>
	function ff() {
	document.getElementById('sample').focus();
	}
	
</script>
<body onload='ff()'><div align='center' class='container'>
<h1 class='header' style='font-family: monospace'>i-BILL<small>[lite]</small></h1>
<strong><span style='float: left'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $date_present;?></span></strong>
<strong><span id='tick2' style='float:right'></span></strong>
<div style='text-align: center;'>
	<a class='navigation' href='index.php'>Make bill</a>
	<a class='navigation'  style='color: #FFF;background-color:#0080ff;' href='search.php'>Search bill</a>
        <a class='navigation' href='populate.php'>Enter product</a>
	<a class='navigation' href='sales.php'>Sales</a>
	<a class='navigation' href='payment.php'>Payment</a>
	<a class='navigation' href='histroy.php'>Histroy</a>
	<a class='navigation' href='buyer.php'>Buyer</a>
</div>
<br>
<div class='buyerflex' style='width:340px;'>
<form method='post' action='search.php'>
	<table cellpadding='9' cellspacing='8'>
	<tr>
		<td>Bill No</td>
		<td><input id='sample' on class='box' type='text' name='billno' value='<?php if(isset($_POST['billno'])){echo $_POST['billno'];}?>' /></td>
	<td><input id='btn' type='submit' name='search' value='SEARCH'></td>
	</tr>
	
</form></table>
</div><br>
<div class='billflex_separate'>

	<div style='background-color:#0080c0;width: auto;'><strong>Bill details</strong></div>
<?php
if((isset($_POST['search'])||isset($_GET['billid']))&&!isset($msg)){
	//to show the bill
?>
<a href='createpdf.php?id=<?php echo $searchbillno;?>'><img src='images.jpeg' width='25' height='25' align='center' ></a>
<a href='?id=<?php echo $searchbillno;?>&email'><img src='email.png' width='25' height='25' align='middle'></a>
<a style='position: absolute;float: right;color: #000;border: groove 2px #000;margin-top: 10px;border-radius:4px;;margin-left: 50px;' href='?return&billid=<?php echo $searchbillno?>'>RETURN GOODS</a>
	<table cellspacing='15' cellpadding='1' align='center'>
		<tr>
			<td><strong>Bill No:&nbsp;</strong>
		<?php echo $searchbillno;?></td>
	            <td><strong>Buyer:&nbsp;</strong>
		   <?php echo $billbuyer;?></td>
		</tr>
	<tr><td><small><u>Purchased on</u></small>
	<br><strong>Date:&nbsp;</strong>
		<?php echo $billdate;?></td>
	<td><br><strong>Time:&nbsp;</strong>
	<?php echo $billtime;?></td>
	</tr>
	<tr><td><strong>Items cart:&nbsp;</strong>
	<?php echo $itemscart;?>&nbsp;<a href='search.php?billid=<?php echo $searchbillno;?>'>View items</a></td>
	<td><strong>Total:&nbsp;</strong>
	Rs.<?php echo $total;?></td>
	</tr>
	</table>

<?php
//end to the bill info
}elseif(isset($msg)){
	echo $msg;
}
if(isset($_GET['billid'])||isset($_GET['return'])){
	$billid=$_GET['billid'];
	$r=$dbclass->select('*','product_base',"bill_no='$billid'",$dbc);?>
	<div class='productdisplay' style='height: 170px;overflow-y: auto;'>
		<table border='1' cellspacing='1' align='center'>
			<?php
			if(!isset($_GET['return']))
			{
			?>
			
			<tr>
				<th>PRODUCT</th>
				<th>PCS</th>
				<th>METER</th>
				<th>Rs.(in ₹)</th>
				<th>TOTAL(in ₹)</th>
				
			</tr>
			<?php
			}else{
			?>
			<tr>
			<th>PRODUCT</th>
			<th>PCS</th>
			<th>REDUCTION</th>
			<form method='post'>
		</tr>
			
	<?php
	}//closing the header for table
	//showing the item
	
	while($row=mysqli_fetch_assoc($r)){
		if(!isset($_GET['return'])){
			
		//getting the carted item from database
					$row['meter']=($row['meter']==0)?'-':$row['meter'];
					$row['pcs']=($row['pcs']==0)?'-':$row['pcs'];
					echo "<tr>
				<td>".$row['product']."</td><td>".$row['pcs']."</td><td>".$row['meter']."</td><td>".$row['price']."</td><td>".
				$row['total']."</td>
				
				</tr>";	
			
			
	}
	else{
	
		//reduction goods
		
		echo"
		
		<tr>
		<td>".$row['product']."</td>
		<td>
		".$row['pcs']."	
		</td>
		<td>
		<input type='number' name='".$row['product']."'>
		</td>
		</tr>
		
		";
		
		
	}	
	}
	if(isset($_GET['return'])){
		echo"
		<input type='submit' name='reduce' value='REDUCE'>
		<input type='hidden' name='billno' value='".$searchbillno."'>
		</form>";
	}
	?>
			</table>
	</div>
			<?php
}


?>


</div>



</div>

<div class='footer' align='center'>Created and owned by<br><img align='center' src='icoders.jpg' width='150' height='37'></div>

</body>
</html>
