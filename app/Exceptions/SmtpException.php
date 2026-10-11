<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Erreur d'envoi SMTP dont le message a ete nettoye (aucun identifiant ni mot de passe).
 * L'exception d'origine n'est volontairement pas chainee : son message contient
 * le nom d'utilisateur SMTP et finirait dans les logs et la table failed_jobs.
 */
class SmtpException extends RuntimeException
{
}
