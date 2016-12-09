
<?php
include_once'connect.php';
include_once'db.php';
	//populating the list
	if($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['add'])){
		$product=$dbclass->clear_string($_POST['product'],$dbc);
		$stock=(isset($_POST['stock'])&&!is_null($_POST['stock']))?$_POST['stock']:NULL;
		$price=(isset($_POST['price'])&&!is_null($_POST['price']))?$_POST['price']:NULL;
		$check=$dbclass->select('*','product_list',"product_name='$product'",$dbc);
		//check for re-entry
		if(mysqli_num_rows($check)==0){
		if(!empty($product)&&!is_null($product)){
			$res=$dbclass->insert('product_list',"VALUES(NULL,'$product','$price','$stock')",$dbc);
			if($res){
				$msg='<strong>'.$product.'</strong> is added';
			}
			else{
				$msg='<strong>'.$product.'</strong> is not added';
			}
		}
		}else{
			$msg='<strong>'.$product.'</strong> is already present';
		}
	}
	elseif($_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['delete'])){
		$product=$_POST['deleteproduct'];
		$check=$dbclass->select('*','product_list',"product_name='$product'",$dbc);
		if(mysqli_num_rows($check)==1){
			$res=$dbclass->delete('product_list',"product_name='$product'",$dbc);
			if($res){
				$msg='<strong>'.$product.'</strong> is deleted';
			}
			else{
				$msg='<strong>'.$product.'</strong> is not deleted';
			}
		}
		else{
			$msg='<strong>'.$product.'</strong> is already deleted';
		}
		
	}
	//updating the list
   if(isset($_POST['update'])){
	$products=(!is_null($_POST['updateproduct']))?$dbclass->clear_string($_POST['updateproduct'],$dbc):NULL;
	$stock=(is_numeric($_POST['stock'])&&!is_null($_POST['stock']))?$_POST['stock']:NULL;
	$prc=(is_numeric($_POST['price'])&&!is_null($_POST['price']))?$_POST['price']:NULL;
	
	if(!is_null($products)&&!is_null($stock)){
		$dbclass->update('product_list',"stock=stock+'$stock'","product_name='$products'",$dbc);
		$msg='<strong>'.$products.'</strong> is Updated with Stock - '.$stock;
	}
	elseif(!is_null($products)&&!is_null($prc)){
		$dbclass->update('product_list',"price='$prc'","product_name='$products'",$dbc);
		$msg='<strong>'.$products.'</strong> is Updated with Price - Rs. '.$prc;
		
	}
   }
   //getting the safety level
			$r=$dbclass->select('*','safety_level',"level>=0",$dbc);
			while($row=mysqli_fetch_assoc($r)){
				$level=$row['level'];
			}
	//set the safety level
	if(isset($_POST['submit'])){
		$change=(!is_null($_POST['level']))?$_POST['level']:NULL;
		$dbclass->update('safety_level',"level='$change'","level='$level' ",$dbc);
		}
		
		//getting the safety level
			$r=$dbclass->select('*','safety_level',"level>=0",$dbc);
			while($row=mysqli_fetch_assoc($r)){
				$level=$row['level'];}
	
	
	
	?>
	

<!DOCTYPE>
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
        <a class='navigation'  style='color: #FFF;background-color:#0080ff;' href='populate.php'>Enter product</a>
	<a class='navigation' href='sales.php'>Sales</a>
	<a class='navigation' href='payment.php'>Payment</a>
	<a class='navigation' href='histroy.php'>Histroy</a>
</div><br>
<div style='height:250px;float: left;'>
<div class='para2'>
<div style='color: #FF0000;font-size: 20px;text-decoration: underline;'>Add Products</div>
	<table cellspacing='5' cellpadding='8'>
	<tr>
		<td>
<form method='post' action='populate.php'>
	<b>New Product</b><input placeholder='Product New' class='box' type='text' name='product' /><br>
	<input class='box' placeholder='Price Per Unit in ₹' type='number' name='price' />
	<input class='box' placeholder='Intial Stock' type='number' name='stock' />
	<br>
	<input id='btn' type='submit' name='add' value='Add' />
	
</form></td>
		</tr><tr><td>
<form method='post' action='populate.php'>
	<b>Delete</b><select class='box' name='deleteproduct'>
		<option value=''></option>
	<?php
	$list=$dbclass->select_normal('*','product_list','ORDER BY idproduct_list ASC',$dbc);
	while($row=mysqli_fetch_assoc($list)){
		echo'<option value='.$row['product_name'].'>'.$row['product_name'].'</option>';
	}	
	?>
	</select>
	<input id='btn' type='submit' name='delete' value='Delete'>
	</form>
		</td>
	</tr>
	</table>
</div><br>

<div class='para2'>
	<div style='color: #FF0000;font-size: 20px;text-decoration: underline;'>Update Stock</div>
	<form method='post'>
		<label><b>Product</b><select class='box' name='updateproduct'>
		<option value=''></option>
	<?php
	$list=$dbclass->select_normal('*','product_list','ORDER BY idproduct_list ASC',$dbc);
	while($row=mysqli_fetch_assoc($list)){
		echo'<option value='.$row['product_name'].'>'.$row['product_name'].'</option>';
	}	
	?>
	</select></label><br>
        <label><b>Stock</b><input type='number' name='stock' placeholder='NO.of.Units' class='box' required='required'><label>
	<br>
		<label><b>Price</b><input type='number' name='price' placeholder='Price' class='box'><label>
		<br>
	<input type='submit' name='update' value='UPDATE' id='btn'>
		
	</form>
	<?php
if(isset($msg)){
	
?>
<?php echo "<br><span style='padding:3px;border:solid 1px;background-color:#FFF;margin:5px;'>".$msg.'</span>';?>
<?php
}?>
</div>
</div>
<div class='buyerflex'>
	<form method='get'>
	<b>SEARCH</b>
	<select class='box' name='product' onchange="this.form.submit();">
		<option> </option>
		<option value='ALL'>All</option>
		<?php
		$t=$dbclass->select_normal('product_name as buyer','product_list',"ORDER BY product_name ASC",$dbc);
		while($e=mysqli_fetch_assoc($t)){
			echo"<option value='".$e['buyer']."'>".$e['buyer']."</option>";
		}
		?>
		
	</select>
</form>	
</div>
<div style='width:340px;float: right;'>

<div class='para2' style='float: right;'>
	<div style='color: #FF0000;font-size: 20px;text-decoration: underline;'> Safety level</div>
	<form method='post'>
		Stock<input type='number' name='level' placeholder='safety level' class='box'>
		<input type='submit' name='submit' value='SET' id='btn'>
		
	</form>
	safety level-<?php echo $level; ?>
	
</div>
<div style='float: right;width: 240px;padding: 5px;margin: 10px;border: solid 1px #000;background-color: #FFF;overflow-y: auto;height: 150px;display: compact;border-radius: 3px;text-align: center;'>
	<h3 style='color:red;'>BELOW SAFETY LEVEL</h3>
	<?php
		//getting the safety level
			$r=$dbclass->select('*','safety_level',"level>=0",$dbc);
			while($row=mysqli_fetch_assoc($r)){
				$level=$row['level'];}
	//checking the safety level
	$r=$dbclass->select('*','product_list',"stock<='$level'",$dbc);
	while($row=mysqli_fetch_assoc($r)){
		?>
		<span style='color: #008040;'><?php echo $row['product_name'];?><br></span>
		<?php
		
	}
	if(mysqli_num_rows($r)==0){
		echo'NO out of STOCK';
		
		}// end of checking
	?>
	
	</div>
</div>
<div class='billflex'>
	<div style='background-color: #0080c0;'><strong>Product List</strong></div>
	<table align='center' cellspacing='10'>
		<tr>
			<th>Name</th>
			<th>Price</th>
			<th>Stock</th>
			<th>Stock value</th>
		</tr>
	<?php
	if(isset($_GET['product'])&&($_GET['product']!='ALL')){
			$pd=$_GET['product'];
		$res=$dbclass->select('product_name as name,price,stock','product_list',"product_name='$pd'",$dbc);
	
	}else{
		
	$res=$dbclass->select_normal('product_name as name,price,stock','product_list','ORDER BY product_name',$dbc);
	}
	
	while($row=mysqli_fetch_assoc($res)){
	
		echo '<tr><td>'.$row['name'].'</td><td>  ₹ '.$row['price']."</td><td> ".$row['stock'].'</td><td>₹ '.$row['stock']*$row['price'].'</td></tr>';
	}
	?>
	</table>
		</div>
<h3><?php
$re=$dbclass->select_normal('SUM(price*stock) AS total','product_list','ORDER BY product_name',$dbc);
while($row=mysqli_fetch_assoc($re)){
	$total=$row['total'];
}
echo 'Total stock value - ₹ '.$total; ?></h3>



</div>



<br><br>
<div class='footer' align='center'>Created and owned by<br><img align='center' src='icoders.jpg' width='150' height='37'></div>

</body>
</html>
