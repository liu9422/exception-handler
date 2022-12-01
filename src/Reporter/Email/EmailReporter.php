<?php
namespace ExceptionHandler\Reporter\Email;

use PHPMailer\PHPMailer\Exception;
use ExceptionHandler\Reporter\Config;
use PHPMailer\PHPMailer\PHPMailer;
use ExceptionHandler\Reporter\Reporter;

/**
 * Class EmailReporter
 * @package Topnew\ExceptionHandler\Reporter\Email
 */
class EmailReporter extends Reporter
{
    /**
     * 发送报告
     * @param Config $config
     * @return bool
     */
    public function send(Config $config)
    {
        $mail = new PHPMailer(true);
        try {
            /* @var $config EmailConfig */
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $config->getHost();
            $mail->SMTPAuth = true;
            $mail->Username = $config->getUsername();
            $mail->Password = $config->getPassword();
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom($config->getFrom(), 'exception');
            if(is_array($config->getAddress())){
                foreach ($config->getAddress() as $value) {
                    $mail->addAddress($value);
                }
            }else{
                $mail->addAddress($config->getAddress());
            }
            $mail->isHTML(true);
            $mail->Subject = $config->getSubject();
            $mail->Body = $config->getBody();
            $mail->AltBody = '您的邮件客户端不支持显示HTML';
            $mail->send();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}