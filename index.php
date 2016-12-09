<?php
	require('fpdf.php');
	include_once'connect.php';
	include_once'db.php';
	//generting new bill
	if(isset($_POST['New'])||isset($_POST['print'])){
		//insert the details of pre bill
			$re=$dbclass->select_normal('*','bill_base',"ORDER BY idbill_base DESC LIMIT 0,1",$dbc);
			while($t=mysqli_fetch_assoc($re)){
				$prebillno=$t['idbill_base'];
			}
			$result=$dbclass->select('*','product_base',"bill_no='$prebillno'",$dbc);
			
			//checking for zero cart avoid empty bill
			if(mysqli_num_rows($result)!=0){
			$total=0;
			$data=$dbclass->select('*','product_base',"bill_no='$prebillno'",$dbc);
			$itemcart=mysqli_num_rows($data);
			while($y=mysqli_fetch_assoc($data)){
				$total=$y['total']+$total;
			}
		$t=$dbclass->select('*','bill_base',"idbill_base='$prebillno'",$dbc);
		while($row=mysqli_fetch_assoc($t)){
                $buyer=$row['buyers_name'];
		$address=$row['buyers_address'];
		}
				
		if(is_numeric($itemcart)&&isset($total)&&!empty($prebillno)&&!is_null($buyer)&&!is_null($address)){
				//inserting tha data
				$r=$dbclass->update('bill_base',"items_cart='$itemcart',total='$total',balance='$total',date='$date_present'","idbill_base='$prebillno'",$dbc);
			
				//reducing the stock of this bill
				$t=$dbclass->select('*','product_base',"bill_no='$prebillno'",$dbc);
				while($y=mysqli_fetch_assoc($t)){
					$name=$y['product'];$quantity=$y['pcs'];
						$dbclass->update('product_list',"stock=stock-$quantity","product_name='$name'",$dbc);
				}
		//printing the bill all details..
		if(isset($_POST['print'])){
		echo"<script type='text/javascript'>
		window.open('/	createpdf.php');
		</script>";
	}
			//checking user needs new bill
			if(isset($_POST['New'])){
				//generating new bill detail
				$r=$dbclass->insert('bill_base',"VALUES(NULL,'$date_present','$time_present',NULL,NULL,NULL,NULL,NULL)",$dbc);
		if($r){
			$res=$dbclass->select_normal('idbill_base as billno','bill_base','ORDER BY idbill_base DESC LIMIT 0,1',$dbc);
			while($row=mysqli_fetch_assoc($res)){
				$billno=$row['billno'];
			}
			
		}
		}
		else{
			if(!isset($billno)){
			$res=$dbclass->select_normal('idbill_base as billno','bill_base','ORDER BY idbill_base DESC LIMIT 0,1',$dbc);
			while($row=mysqli_fetch_assoc($res)){
				$billno=$row['billno'];
				
			}
		}
		}
		}else{
			if(!isset($billno)){
			$res=$dbclass->select_normal('idbill_base as billno','bill_base','ORDER BY idbill_base DESC LIMIT 0,1',$dbc);
			while($row=mysqli_fetch_assoc($res)){
				$billno=$row['billno'];
				
			}
			$msg='PLEASE SET THE BUYER\'s DETAILS';	
		}
			
		}
				
		
			}
			else{
				if(!isset($billno)){
			$res=$dbclass->select_normal('idbill_base as billno','bill_base','ORDER BY idbill_base DESC LIMIT 0,1',$dbc);
			while($row=mysqli_fetch_assoc($res)){
				$billno=$row['billno'];
			
			}
		}
			}
		
		
	}
	else{//gaining the same bill number
		
		if(!isset($billno)){
			$res=$dbclass->select_normal('idbill_base as billno','bill_base','ORDER BY idbill_base DESC LIMIT 0,1',$dbc);
			while($row=mysqli_fetch_assoc($res)){
				$billno=$row['billno'];
				
			}
		}
		
		
	}
	
	//adding to the cart
	if(isset($_POST['add'])){
		$product=$_POST['product'];
		$meter=(is_numeric($_POST['meter']))?$dbclass->clear_string($_POST['meter'],$dbc):" ";
		$pcs=$dbclass->clear_string($_POST['pcs'],$dbc);
		$rs=(is_numeric($_POST['cost']))?$dbclass->clear_string($_POST['cost'],$dbc):" ";
			//checking the cost if not given taking the default value
		if($rs==" "){
			$y=$dbclass->select('price','product_list',"product_name='$product'",$dbc);
			while($t=mysqli_fetch_assoc($y)){
				$rs=$t['price'];
			}
		}
		if(!empty($_POST['meter'])&&$_POST['meter']!=" "&&$pcs==0){
			$totalcost=$meter*$rs;
			$pcs=0;
		}
		elseif(!empty($pcs)&&$meter==0){
			$totalcost=$pcs*$rs;
			$meter=0;
		}elseif($meter!=0&&$pcs!=0){
			$totalcost=$pcs*$meter*$rs;
		}
		if(!empty($product)&&!empty($rs)&&$rs!=" "&&!empty($totalcost)){
			//avoid duplicate cart..
			$check=$dbclass->select('*','product_base',"bill_no='$billno' AND product='$product' AND (meter='$meter' OR pcs='$pcs') AND price='$rs'",$dbc);
			if(mysqli_num_rows($check)==0){
			$dbclass->insert('product_base',"VALUES(NULL,'$billno','$product','$pcs','$meter','$rs','$totalcost')",$dbc);
			}
		}
		
	}
	//removing the item from the bill
	if(isset($_GET['removeid'])){
		$id=$_GET['removeid'];
		$dbclass->delete('product_base',"idproduct_base='$id'",$dbc);
	}
	//clearing the bill
	if(isset($_POST['clear'])){
		$dbclass->delete('product_base',"bill_no='$billno'",$dbc);
	}
	
	// adding the buyer name and address
	if(isset($_POST['buyernameset'])||isset($_POST['buyersname_old'])){
			//getting the buyers info
			if(isset($_POST['buyername'])&&$_POST['buyersname_old']==''){
				$buyer=$dbclass->clear_string($_POST['buyername'],$dbc);
					$address=$dbclass->clear_string($_POST['address'],$dbc);
					$email=$dbclass->clear_string($_POST['email'],$dbc);
					$ph=$dbclass->clear_string($_POST['phone'],$dbc);
					$check=$dbclass->select('*','buyer_base',"buyers_name='$buyer'",$dbc);
					//avoid duplicate entry checking
					if(mysqli_num_rows($check)==0){
					$dbclass->insert('buyer_base',"VALUES(NULL,'$buyer','$address','$ph','$email')",$dbc);
					}
			}
			elseif(isset($_POST['buyersname_old'])){
				$buyer=$_POST['buyersname_old'];
				
			}
			
				if(isset($_POST['buyersname_old'])&&$_POST['buyersname_old']!=''){
					$select=$dbclass->select('*','buyer_base',"buyers_name='$buyer' LIMIT 0,1 ",$dbc);
					while($row=mysqli_fetch_assoc($select)){
						$address=$row['buyers_address'];
					}
				}
				else{
					$address=$dbclass->clear_string($_POST['address'],$dbc);
				}
				if(!empty($buyer)&&!empty($address)){
				$r=$dbclass->update('bill_base',"buyers_name='$buyer',buyers_address='$address'","idbill_base='$billno'",$dbc);
				}
		
	}
	
	?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>i-BILL</title>
	<meta charset='uft-8'>
	<link href='stylesheet.css' type='text/css' rel='stylesheet'>
	<?php
	$res=$dbclass->select('*','product_base',"bill_no='$billno'",$dbc);
	$num=mysqli_num_rows($res);
	?>
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
	
	function border(obj){
	
	obj.style.padding='10px';
		obj.style.color='blue';
		obj.style.bordercolor='blue';
	
	}
	function border1(obj) {

			obj.style.padding='4px';
			obj.style.color='black';
				obj.style.bordercolor='black';
	}
</script>
			
</head>

<body onload='ff()'><div align='center' class='container'>
<h1 class='header' style='font-family: monospace'>i-BILL<small>[lite]</small></h1>
<strong><span style='float: left'>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $date_present;?></span></strong>
<strong><span id='tick2' style='float:right'></span></strong>
<div style='text-align: center;'>
	<a class='navigation' style='color: #FFF;background-color:#0080ff; ' href='index.php'>Make bill</a>
	<a class='navigation' href='search.php'>Search bill</a>
        <a class='navigation' href='populate.php'>Enter product</a>
	<a class='navigation' href='sales.php'>Sales</a>
	<a class='navigation' href='payment.php'>Payment</a>
	<a class='navigation' href='histroy.php'>Histroy</a>
	<a class='navigation' href='buyer.php'>Buyer</a>
</div>
<?php
// get the buyer name if set
$r=$dbclass->select('*','bill_base',"idbill_base='$billno'",$dbc);
while($row=mysqli_fetch_assoc($r)){
	$buyerset=$row['buyers_name'];
}

?>

<br>
		<div class='buyersflex'>
			<form method='post'>
<strong>Bill details</strong><table align='center'>
	
		<tr><td>Registered Buyer<br>
		<select onchange="this.form.submit();"  onfocusin='border(this)' onfocusout='border1(this)' name='buyersname_old' class='box'>
	<option value=''></option>
		<?php
		$r=$dbclass->select_normal(' buyers_name','buyer_base','ORDER BY buyers_name',$dbc);
		while($row=mysqli_fetch_assoc($r)){
			if(!is_null($row['buyers_name'])&&!empty($row['buyers_name'])){
			echo"<option value='".$row['buyers_name']."'>".$row['buyers_name']."</option>";
			}
		}
		?>
	</select></td></tr>
		<tr><td>Buyer Name<br>
			<input  onfocusin='border(this)' onfocusout='border1(this)' class='box' type='text' name='buyername' placeholder="Buyer's name" value='<?php if(isset($_POST['buyername'])&&!isset($_POST['New'])){echo $_POST['buyername'];}?>'>
		</td></tr>
	<tr>
	<td>Address<br>
		<textarea  onfocusin='border(this)' onfocusout='border1(this)' class='box' name='address' placeholder='address of buyer'><?php if(isset($_POST['address'])&&!isset($_POST['New'])){echo $_POST['address'];}?></textarea></td>
	</tr>	
		<tr><td>Email<br>
			<input  onfocusin='border(this)' onfocusout='border1(this)' class='box' type='email' name='email' placeholder="Email id" value='<?php if(isset($_POST['email'])&&!isset($_POST['New'])){echo $_POST['email'];}?>'>
		</td></tr>
			<tr><td>Phone No.<br>
			<input  onfocusin='border(this)' onfocusout='border1(this)' class='box' type='number' name='phone' placeholder="Phone number" value='<?php if(isset($_POST['phone'])&&!isset($_POST['New'])){echo $_POST['phone'];}?>'>
		</td></tr>
	
	
	<tr>
	<td><input type='submit' name='buyernameset' value='ADD' id='btn'></td>
	</tr>
	</table>
	</form>
	
	</div>
<div class='para' style='float: left;position: absolute;'>
	<span style=''><div style="color:#ff0000;font-size: 20px;"><strong>Bill no&nbsp;-&nbsp;<?php echo $billno; ?></strong></div>
	<div style='font-size: 15px;color: #008000;'>Items cart-<?php echo $num;?></div>
	<b><small><u>Buyer Name</u></small><br>
	<span style='color:#0000ff;font-family: sans-serif;font-size: 20px;'>
	<?php if(isset($buyerset)&&!is_null($buyerset)){echo $buyerset;}else{echo 'Not yet Set';} ?>
	</span>
	</span>
	<hr>
	
		Today's
		<table align='center' cellspacing='10' style='font-size: 15px;'>
			<tr>
				<th>
				
                              sales	
				</td>
				<th>
					No of Bills
				</td>
</tr>
<?php
// ca;culating the sale
$r=$dbclass->select('SUM(total) as sale','bill_base',"date='$date_present'",$dbc);

$num=$dbclass->select('*','bill_base',"date='$date_present'AND total>=1",$dbc);
while($row=mysqli_fetch_assoc($r)){
	?>
	<td>
		<?php if(!is_null($row['sale'])){echo '₹'." ".$row['sale'];}else{echo'₹ 0';}?>
	</td>
	<td>
		<?php if(mysqli_num_rows($num)!=0){echo mysqli_num_rows($num);}else{echo'0';}?>	
	</td>
	<?php
}
?><hr>
</table>
		<hr>
		Previous bills
		<hr>
			<table  cellspacing='3' align='center' style='font-size: 10px;'>
			<strong><small><tr><th>Bill no</th>
				<th>Buyer</th>
				<th>Amount</th></tr></small></strong>	
		<?php
		//showing the prevoius bill
		$t=$dbclass->select('*','bill_base',"date='$date_present'ORDER BY idbill_base DESC LIMIT 0,7 ",$dbc) ;
		if(mysqli_num_rows($t)!=0){
		while($row=mysqli_fetch_assoc($t)){
			if(!is_null($row['total']))
		echo" <tr><td><small>".$row['idbill_base']."&nbsp;</td><td> ".$row['buyers_name'].'</td><td>₹'.$row['total'].'</td></tr></small>';	
		}
		}else{
			
		}
		?>
			</table>
</div>
 
 <div align='center' style='width: 550px;;text-align: center;position: relative;'>
<table align='center' style='background-color: #ffffaa;width: 550px;'>
	<form method='post'>
	<tr>
		<td>
			<b>Product</b><br><select  id='here' onfocusin='border(this)' onfocusout='border1(this)' id='here' class='box' name='product'>
		<?php
		// showing the products from database
		$result=$dbclass->select_normal('*','product_list','ORDER BY idproduct_list ASC',$dbc);
		
		while($row=mysqli_fetch_assoc($result)){
			if($row['stock']){
					echo"<option value=".$row['product_name'].">".$row['product_name']."</option>";
				
			}
		}
		
		?>	
		</select></td>
		<td>
			<b>Pcs</b><br><input id='1' onfocusin='border(this)' onfocusout='border1(this)'  size='7'  type='number' name='pcs'>
		</td>
		<td>
			<b>Meters</b><br><input id='1' onfocusin='border(this)' onfocusout='border1(this)' size='7'   type='text' name='meter'>
		</td>
		<td>
			<b>Rs</b><br><input id='1' class=='box' onfocusin='border(this)' onfocusout='border1(this)' size='7' type='text' name='cost'>
		</td>
		<td>
			<input id='btn' type='submit' name='add' value='Add'>
		</td>
		
	</tr>
	</table>
<?php
	if(isset($msg)){
		echo"<div class='warning'>";
		echo '<strong>'.$msg.'</strong>';
		echo'</div>';
	}
	?>
<div class='productdisplay'>
		<table  border='1' cellspacing='1' align='center' style='border: solid 1px #000;width: 100%;'>
			<tr>
				<th>PRODUCT</th>
				<th>PCS</th>
				<th>METER</th>
				<th>Rs.</th>
				<th>TOTAL</th>
				<th>Action</th>
			</tr>
			<?php
			$re=$dbclass->select('*','product_base',"bill_no='$billno' ORDER BY idproduct_base DESC",$dbc);
			while($row=mysqli_fetch_assoc($re)){
					$row['meter']=($row['meter']==0)?'-':$row['meter'];
					$row['pcs']=($row['pcs']==0)?'-':$row['pcs'];
					echo "<tr>
				<td >".$row['product']."</td><td>".$row['pcs']."</td><td>".$row['meter']."</td><td>".$row['price']."</td><td>".
				$row['total']."</td>
				<td><a href='index.php?removeid=".$row['idproduct_base']."'><img src='delete_grey.gif'></a></td>
				</tr></form>";	
			}
			?>
		</table>
	</div>
</div>
<div style='width: 500px;'>
<form method='post'>
	<table style='margin-top: 20px;' cellspacing='8'>
		
		<tbody align='center'>
			<tr>
				<td><input id='btn' type='submit' name='print' value='print'></td>
				<td><input id='btn' type='submit' name='clear' value='clear'></td>
				<td><input id='btn' type='submit' name='New' value='New'></td>	
				
				<?php
				$total=0.00;
			//calculating the cost
			$res= $dbclass->select('*','product_base',"bill_no='$billno'",$dbc);
			while($row=mysqli_fetch_assoc($res)){
				$total=$row['total']+$total;
			}
				?>
				
				<td><span class='total'><strong>Grand Total:&nbsp;&nbsp;<?php echo '₹'." " .$total;?></strong></span></td>
			</tr>
		</tbody>
	
		
	</table>
	</div>
	<br>
	<?php
	//bringing the last bill info
	$r=$dbclass->select_normal('*','bill_base',"WHERE total>=1 ORDER BY idbill_base DESC LIMIT 0,1",$dbc);
	while($row=mysqli_fetch_assoc($r)){
		echo"<div align='center' style='width:350px;'>";
	echo"<marquee scrollamount='3'><span style='color:#FF0000;background-color:#ffff00;'><u>Last Bill no</u>: &nbsp;".$row['idbill_base']."  &nbsp;<u>Buyer</u>:  &nbsp;".$row['buyers_name']." &nbsp; <u>Items cart</u>:  &nbsp;".$row['items_cart']."  &nbsp;<u>Total:</u>  ₹&nbsp;".$row['total']." </marquee></span>";
		echo '</div>';
	}
	?>

		
	<div class='footer' align='center' >Created and owned by</div><img src='icoders.jpg' width='150' height='37'>
	
</body>
</html>
