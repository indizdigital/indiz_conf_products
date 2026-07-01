<?php
namespace Indiz\Products\Services;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Mailer{

    public function send(string $receiver,string $cc,string $bcc,string $subject,string $template,array $variables = []): void
    {
        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $fromAdress = $GLOBALS['TYPO3_CONF_VARS']['MAIL']["defaultMailFromAddress"];
        $email
            ->to($receiver)
            ->from($fromAdress)
            ->cc($cc)
            ->bcc($bcc)
            ->subject($subject)
            ->format('html') // or 'both'
            ->setTemplate($template) // Resources/Private/Templates/Email/MyTemplate.html
            
            ->assignMultiple($variables);

        $mailer = GeneralUtility::makeInstance(MailerInterface::class);
        $mailer->send($email);
    }
}