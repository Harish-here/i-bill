
<?php
require 'PHPMailer/PHPMailerAutoload.php';
require'createpdf.php';


//getting the id
if(isset($_GET['id'])&&isset($_GET['email'])){
    $id=$_GET['id'];
}
$detail=$dbclass->select('*','bill_base',"idbill_base='$id'",$dbc);// selecting the buyer of that bill
while($r=mysqli_fetch_assoc($detail)){
    $name=$r['buyers_name'];
    $emaildetail=$dbclass->select('*','buyer_base',"buyers_name='$name'",$dbc);//getting the buyer email
    if(mysqli_num_rows($emaildetail)!=0){
    while($t=mysqli_fetch_assoc($emaildetail)){
        $email=$t['buyers_email'];
        $buyeremail=true;
    }
    }else{
        $buyeremail=false;
    }
}
if($buyeremail){
$file="C:/apache/htdocs/bill_".$id.".pdf";
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'sandeepsangam23@gmail.com';
$mail->Password = '9677830650';
$mail->SMTPSecure = 'tls';
$mail->From = 'sandeepsangam23@gmail.com';
$mail->FromName = 'SANGAM FASHIONS';
$mail->addAddress($email, $name);
$mail->addReplyTo('sandeepsangam23@gmail.com', 'SANGAM FASHIONS');
$mail->AddAttachment($file);
$mail->WordWrap = 50;
$mail->isHTML(true);
$mail->Subject = 'INVOICE FROM SANGAM FASHIONS';
$mail->Body    = "<p>Your Purchase Invoice have been attached with this mail</p>
<p><u>For further contact:</u>
<br>
Reply to this mail | 
phone no:9677830650
</p>
<br><br>
<i>Powered by i-BILL lite.</i><div style='font-size:14px;' align='right' ><b>Created and owned by<br><a href='www.i-coders.in'>i-CODERS Web solutions</a></b></div>";
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}
echo '<b>Message has been sent</b>';
}
else{
    echo '<b>BUYER EMAIL id NOT FOUND</b>';
}