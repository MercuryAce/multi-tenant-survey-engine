<?php
declare(strict_types=1);

namespace Survey\Service;

use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\I18n\DateTime;
use Survey\Mailer\InvitationMailer;

class InvitationService
{
    use LocatorAwareTrait;

    /**
     * @return list<object>
     */
    public function issueMany(int $surveyId, ?int $nodeId, array|string $emails, bool $send = true, ?int $userId = null): array
    {
        $created = [];
        foreach ($this->normaliseEmails($emails) as $email) {
            $created[] = $this->issueAndSend($surveyId, $nodeId, $email, $send, $userId);
        }

        return $created;
    }

    public function issueAndSend(int $surveyId, ?int $nodeId, ?string $email, bool $send = true, ?int $userId = null): object
    {
        $invite = $this->fetchTable('Survey.Invitations')->issue($surveyId, $nodeId, $email, $userId);
        if ($send && $email) {
            $this->send($invite);
        }

        return $invite;
    }

    public function send(object $invitation): bool
    {
        if (!$invitation->email) {
            return false;
        }

        $survey = $this->fetchTable('Survey.Surveys')
            ->find()
            ->applyOptions(['skipTenant' => true])
            ->where(['id' => $invitation->survey_id])
            ->firstOrFail();
        $tenant = $this->fetchTable('AdminKit.Tenants')->get($survey->tenant_id);

        try {
            $mailer = new InvitationMailer('default');
            $mailer->send('invite', [$invitation, $survey, $tenant]);
            $invitation->sent_at = DateTime::now();
            $invitation->sent_count = (int)$invitation->sent_count + 1;
            $this->fetchTable('Survey.Invitations')->saveOrFail($invitation);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Invitation email failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param list<string>|string $emails
     * @return list<string>
     */
    public function normaliseEmails(array|string $emails): array
    {
        $parts = is_string($emails) ? [$emails] : $emails;
        $out = [];
        foreach ($parts as $part) {
            foreach (preg_split('/[\s,;]+/', (string)$part) ?: [] as $email) {
                $email = strtolower(trim($email));
                if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $out[] = $email;
                }
            }
        }

        return array_values(array_unique($out));
    }
}
