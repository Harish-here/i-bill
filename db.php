<?php
class db{
	public function show($table,$dbc){
		$q="SHOW columns FROM ".$table;
		$result=mysqli_query($dbc,$q) or mysqli_error($dbc);
		echo mysqli_error($dbc);
		return $result;
		
	}
public function select($field='*',$table,$conditions="NULL",$dbc){
	$query="SELECT ".$field." FROM ".$table." WHERE ".$conditions." ";
	$result=mysqli_query($dbc,$query)or die(mysqli_error($dbc));
	return $result;
	
}

public function select_normal($field='*',$table,$conditions,$dbc){
	$query="SELECT ".$field." FROM ".$table." ".$conditions." ";
	
	$result=mysqli_query($dbc,$query)or die(mysqli_error($dbc));
	return $result;
	
}
public function insert($table,$values,$dbc){$col='(';$field[]=array();$w=1;
	$r=$this->show($table,$dbc);
	while($row=mysqli_fetch_array($r)){
		$field[$w]=$row[0];
		$w++;
	}
	for($s=1;$s<$w;$s++){
		$col=$col.$field[$s];
		if($s!=($w-1)){
			$col=$col.',';
		}		
		
	}
	$col=$col.')';
	$query="INSERT INTO ".$table.$col." ".$values." ";
	$result=mysqli_query($dbc,$query)or die(mysqli_error($dbc));
	
	return $result;
}
public function delete($table,$conditions,$dbc){
	$query="DELETE FROM ".$table." WHERE ".$conditions."";
	$result=mysqli_query($dbc,$query)or die(mysqli_error($dbc));
	return $result;
}
public function update($table,$change,$conditions,$dbc){
	$query="UPDATE ".$table." SET ".$change." WHERE ".$conditions."";
	$result=mysqli_query($dbc,$query) or mysqli_error($dbc);
	return $result;
}
public function clear_string($string,$dbc)
{
	$changed=mysqli_real_escape_string($dbc,htmlspecialchars($string));
	return $changed;
}
};
$dbclass=new db;
?>