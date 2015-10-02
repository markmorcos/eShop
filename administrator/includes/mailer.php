<?php 
/*$emailTo=$_REQUEST['emailTo'];
$fromName=$_REQUEST['fromName'];
$fromAddress=$_REQUEST['fromAddress'];
$emailSubject=$_REQUEST['emailSubject'];
$body=$_REQUEST['body'];
$mime_boundary=$_REQUEST['mime_boundary'];
$isValid=$_REQUEST['isValid'];
$email=$_REQUEST['email'];*/


$_SESSION["key"] = "value";


function sendMail($emailTo, $fromName, $fromAddress, $Ccs="", $Bccs="", $emailSubject, $body){
	
	// convert $to into an array
    if(is_scalar($emailTo))
        $to = array($emailTo);
   
    // BEGIN VALIDATION
    // e-mail address validation
    $e = "/^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,4}$/i";
    // from address
    if(!preg_match($e, $fromAddress)){ 
		
		echo $fromAddress . " is invalid email address format in From.<br />";
		return false;
	}
    // to address(es)
    foreach($to as $rcpt)
	{
	     if(!preg_match($e, $rcpt)){
		 	echo $rcpt . " is invalid email address format.<br />";
			return false;
		 }
    }
   
    # subject validation (only printable 7-bit ascii characters allowed)
    # needs to be adapted to allow for foreign languages with 8-bit characters
    if(!preg_match("/^[\\040-\\176]+$/", $emailSubject)){
		echo $emailSubject. " is invalid email subject format.<br />";
		return false;
	}

	# Is the OS Windows or Mac or Linux 
	if(strtoupper(substr(PHP_OS,0,3)=='WIN')){
      $eol="\r\n";
    }elseif(strtoupper(substr(PHP_OS,0,3)=='MAC')){
      $eol="\r";
    }else{
      $eol="\n";
    }
	
	# -=-=-=- MIME BOUNDARY
	$mime_boundary = "----www.DinaCantina.com----".md5(time());
	
	$charset='UTF-8';
	
 # Common Headers
  $headers .= "From: ".$fromName."<".$fromAddress.">".$eol;
  
  # Carbon Copy(ies)
  if ($Ccs != ""){
  	// convert $Ccs into an array
    if(is_scalar($Ccs))
        $cc = array($Ccs);
		
	// Cc address(es)
    foreach($cc as $rcpt){
	
        if(!preg_match($e, $rcpt)){
		 	echo $rcpt . " is invalid email address format (Cc).<br />";
		}
		else
			$headers .= 'Cc:'. $cc . $eol;	
    }

  }
  
  # Blind Carbon Copy(ies)
  if ($Bccs != ""){
  	// convert $Bccs into an array
    if(is_scalar($Bccs))
        $bcc = array($Bccs);
		
	// Bcc address(es)
    foreach($bcc as $rcpt){
	
        if(!preg_match($e, $rcpt)){
		 	echo $rcpt . " is invalid email address format (Bcc).<br />";
		}
		else
			$headers .= 'Bcc:'. $bcc . $eol;	
    }
  }

  $headers .= "Reply-To: ".$fromName."<".$fromAddress.">".$eol;
  $headers .= "Return-Path: ".$fromName."<".$fromAddress.">".$eol;    // these two to set reply address
  $headers .= 'Sender-IP: '.$_SERVER["REMOTE_ADDR"].$eol;
  $headers .= "Message-ID: <".time()."-".$_SERVER['SERVER_NAME']."-".$fromAddress.">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
  $headers .= 'Date: '.date("r").$eol;
  
  # Boundry for marking the split & Multitype Headers
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/mixed; boundary=\"".$mime_boundary."\"".$eol;
  
  # Open the first part of the mail
  $message = $mime_boundary.$eol;
  
  # Text Version
  /*$message .= "--".$mime_boundary.$eol;
  $message .= "Content-Type: text/plain; charset=UTF-8".$eol;
  $message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
  $message .= strip_tags(str_replace("<br>", "\n", substr($body, (strpos($body, "<body>")+6)))).$eol.$eol;*/

  # HTML Version	  
  $message .= "--".$mime_boundary.$eol;
  $message .= "Content-Type: text/HTML; charset=UTF-8".$eol;
  $message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
  $message .= $body.$eol.$eol;
  
  $message .= "--".$mime_boundary."--".$eol.$eol;
  
  # In case any of our lines are larger than 70 characters, we should use wordwrap()
  $message = wordwrap($message, 70);
  
  # SEND THE EMAIL
  ini_set(sendmail_from,$fromAddress);  // the INI lines are to force the From Address to be used !
  $mail_sent = @mail($emailTo, $emailSubject, $message, $headers);
  ini_restore(sendmail_from);
 
  return $mail_sent;
}
// *_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*

$in_str=$_REQUEST['in_str'];
$charset=$_REQUEST['charset'];
$start=$_REQUEST['start'];

function encode($in_str, $charset) {
    $out_str = $in_str;
    if ($out_str && $charset) {

        // define start delimimter, end delimiter and spacer
        $end = "?=";
        $start = "=?" . $charset . "?B?";
        $spacer = $end . "\r\n " . $start;

        // determine length of encoded text within chunks
        // and ensure length is even
        $length = 75 - strlen($start) - strlen($end);
        $length = floor($length/2) * 2;

        // encode the string and split it into chunks
        // with spacers after each chunk
        $out_str = base64_encode($out_str);
        $out_str = chunk_split($out_str, $length, $spacer);

        // remove trailing spacer and
        // add start and end delimiters
        $spacer = preg_quote($spacer);
        $out_str = preg_replace("/" . $spacer . "$/", "", $out_str);
        $out_str = $start . $out_str . $end;
    }
    return $out_str;
} 
// *_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*
/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/

function validateEmail($email){
	// First, we check that there's one @ symbol, 
  // and that the lengths are right.
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || 
 checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
/* 	  else if(!(fsockopen($domain, 25, $errno, $errstr, 30) || fsockopen($domain, 80, $errno, $errstr, 30))) 
      {
	  	// we check if port 25 is open for that domain name, 
		// which makes sure that the domain name is in use.
        $isValid = false; 
      } */
   }
   return $isValid;
}
// 
?>
