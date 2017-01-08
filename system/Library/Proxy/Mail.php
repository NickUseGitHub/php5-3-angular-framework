<?php  
namespace Application\Library\Proxy;

use Application\Library\Dependency\IDependency;

class Mail
{
    const APPLICATION_EMAIL_HOST = "ssl://smtp.googlemail.com"; // "smtp.gmail.com";
    const APPLICATION_EMAIL_PORT = 465; //587
    const APPLICATION_EMAIL_GOOGLE = "";
    const APPLICATION_EMAIL_PASSWORD = "";

    public static function getConfigedMail(IDependency $mail)
    {
        if (empty($mail)) {
            return $mail;
        }

        $mail->CharSet = "utf-8";
		$mail->IsSMTP();
		$mail->SMTPDebug = 0; //2 = debug mode
		$mail->SMTPAuth = true;
		// $mail->SMTPSecure = "tls";
		// $mail->SMTPSecure = 'ssl';
		$mail->Host = self::APPLICATION_EMAIL_HOST;// "smtp.gmail.com";
		$mail->Port = self::APPLICATION_EMAIL_PORT; //587;
		$mail->Username = self::APPLICATION_EMAIL_GOOGLE; //"bolandeveloper@gmail.com";
		$mail->Password = self::APPLICATION_EMAIL_PASSWORD;

        return $mail;
    }

    public static function send(IDependency $mail, $mailDetail)
    {
        if (empty($mail)) {
            return false;
        }

        $from = $mailDetail['from'];
        $subject = $mailDetail['subject'];
        $body = $mailDetail['body'];
        $email_to = $mailDetail['email_to'];

        $mail->SetFrom($from, "");
		// $mail->AddReplyTo("admin@jaspalhome.com", "Jaspalhome.com");
		$mail->Subject = $subject;
		
		$mail->MsgHTML($body);
		if(is_array($email_to)){
			foreach($email_to as $e_to)
				$mail->AddAddress($e_to);
		}
		else $mail->AddAddress($email_to);
		$mail->Send();
		return true;
    }
}