<?php

require('fpdf.php');
include_once'connect.php';
include_once'db.php';
ob_end_clean();
if(!isset($_GET['id'])){
$res=$dbclass->select_normal('*','bill_base','ORDER BY idbill_base DESC LIMIT 0,1',$dbc);
}
else{
    $id=$_GET['id'];
$res=$dbclass->select('*','bill_base',"idbill_base='$id'",$dbc);
}
while($row=mysqli_fetch_assoc($res)){
    $billno=$row['idbill_base'];
    $total=$row['total'];
    $date=$row['date'];
    $time=$row['time'];
    $buyer=$row['buyers_name'];
    $address= $row['buyers_address'];
    $same=$buyer.'\n'.$address;
}
class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('sangambanner.jpg',1,1,208,50);
    $this->Image('middlebanner.jpg',1,53,208,0);
    
}

// Page footer
function Footer()
{ 
    $this->Image('footer.jpg',1,247,208,0);
    // Position at 1.5 cm from bottom
    $this->SetY(-10);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.'generated by i-BILL',0,0,'L');
    $this->Cell(-31,10,'powered by',0,0,'R');
    $this->Image('icoders.jpg',170,287,9*2.6,3*2);
}

}
// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',14);
$pdf->Text(20,46.8,$billno);
$pdf->Text(173,47,$date);
$pdf->SetY(58);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(90,10,$buyer,0,0,'C');
$pdf->ln();
$pdf->SetFont('Arial','',14);
$pdf->MultiCell(90,7,$address,0,'C');
$pdf->SetY(103);
$pdf->SetDrawColor(0,50,250);
$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(10,7,'s.no',1,0,'C');
$pdf->Cell(70,7,'Product',1,0,'C');
$pdf->Cell(20,7,'Pcs',1,0,'C');
$pdf->Cell(20,7,'Meter',1,0,'C');
$pdf->cell(30,7,'Rate (in Rs.)',1,0,'C');
$pdf->cell(40,7,'Amount (in Rs.)',1,0,'C');
$pdf->ln();
$sno=1;
$res=$dbclass->select('*','product_base',"bill_no='$billno'",$dbc);
while($row=mysqli_fetch_assoc($res)){
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('Arial','',14);
    $pdf->Cell(10,8,$sno,1,0,'C');
$pdf->Cell(70,8,$row['product'],1,0,'C');
$pdf->Cell(20,8,$row['pcs'],1,0,'C');
$pdf->Cell(20,8,$row['meter'],1,0,'C');
$pdf->cell(30,8,$row['price'],1,0,'C');
$pdf->cell(40,8,$row['total'],1,0,'C');
$pdf->ln();
$sno++;
if($sno==17||$sno==34||$sno==51){
$pdf->AddPage();
$pdf->Text(20,46.8,$billno);
$pdf->Text(173,47,$date);
$pdf->SetY(103);
$pdf->SetDrawColor(0,50,250);
$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(10,7,'s.no',1,0,'C');
$pdf->Cell(70,7,'Product',1,0,'C');
$pdf->Cell(20,7,'Pcs',1,0,'C');
$pdf->Cell(20,7,'Meter',1,0,'C');
$pdf->cell(30,7,'Rate(in Rs.)',1,0,'C');
$pdf->cell(40,7,'Amount(in Rs.)',1,0,'C');
$pdf->ln();
}
}

$pdf->SetY(235);
$pdf->SetDrawColor(0,50,250);
$pdf->SetTextColor(0, 0,0);
$pdf->SetFont('Arial','B',14);
$pdf->cell(150,9,'TOTAL',1,0,'C');
$pdf->cell(40,9,$total,1,0,'C');
$pdf->SetTextColor(0, 0,0);
if(isset($_GET['email'])){
    $pdf->Output('bill_'.$billno.'.pdf','F');
}
elseif(!isset($_GET['email'])){
    $pdf->Output('/bill/bill_'.$billno.'.pdf','I');
}


?>