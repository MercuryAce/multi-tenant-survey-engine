<?php
declare(strict_types=1);

namespace Survey\Mailer;

use Cake\Mailer\Mailer;
use Cake\Routing\Router;

class InvitationMailer extends Mailer
{
    public function invite(object $invitation, object $survey, object $tenant): void
    {
        $url = Router::url('/take/' . $invitation->token, true);
        $from = (string)($tenant->support_email ?: 'noreply@localhost');
        $brand = (string)($tenant->brand_name ?: 'Engagement Platform');

        $this
            ->setTo((string)$invitation->email)
            ->setFrom([$from => $brand])
            ->setSubject('You are invited: ' . $survey->title)
            ->setEmailFormat('both')
            ->setViewVars([
                'survey' => $survey,
                'tenant' => $tenant,
                'url' => $url,
                'brand' => $brand,
            ]);

        $this->viewBuilder()
            ->setTemplate('invite')
            ->setLayout('default')
            ->setPlugin('Survey');
    }
}
