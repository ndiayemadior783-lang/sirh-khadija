<?php

namespace App\Mail;

use App\Exceptions\SmtpException;
use App\Services\MailConfig;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Remplace les erreurs du transport SMTP par un message sans identifiants,
 * pour les envois immediats comme pour ceux de la file d'attente.
 */
trait EnvoiSmtpSecurise
{
    public function send($mailer)
    {
        try {
            return parent::send($mailer);
        } catch (TransportExceptionInterface $e) {
            throw new SmtpException(MailConfig::messageErreur($e));
        }
    }
}
