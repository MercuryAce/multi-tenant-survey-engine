<?php
declare(strict_types=1);

namespace Survey\Controller;

use Cake\Http\Response;
use Survey\Service\InvitationService;

class InvitationsController extends AppController
{
    public function index(): void
    {
        $surveyId = (int)$this->request->getQuery('survey_id');
        $query = $this->fetchTable('Survey.Invitations')
            ->find()
            ->contain(['Surveys', 'OrganisationNodes'])
            ->orderBy(['Invitations.created' => 'DESC']);
        if ($surveyId) {
            $query->where(['Invitations.survey_id' => $surveyId]);
        }
        $invitations = $this->paginate($query);
        $survey = $surveyId ? $this->fetchTable('Survey.Surveys')->get($surveyId) : null;
        $this->set(compact('invitations', 'survey', 'surveyId'));
    }

    public function add(?InvitationService $invites = null): ?Response
    {
        $invites ??= new InvitationService();
        $surveyId = (int)($this->request->getData('survey_id') ?: $this->request->getQuery('survey_id'));
        if ($this->request->is('post')) {
            $nodeId = $this->request->getData('organisation_node_id')
                ? (int)$this->request->getData('organisation_node_id')
                : null;
            $send = !in_array($this->request->getData('send_email'), [null, '', '0', false], true);
            $emails = $invites->normaliseEmails((string)$this->request->getData('emails'));
            $single = trim((string)$this->request->getData('email'));
            if ($single !== '') {
                $emails = array_values(array_unique([...$emails, ...$invites->normaliseEmails($single)]));
            }
            if ($emails === []) {
                $this->Flash->error('Enter at least one valid email address.');
            } else {
                $created = $invites->issueMany($surveyId, $nodeId, $emails, $send);
                $sent = 0;
                foreach ($created as $invite) {
                    if ($invite->sent_at) {
                        $sent++;
                    }
                }
                $this->Flash->success(sprintf(
                    '%d invitation%s created%s.',
                    count($created),
                    count($created) === 1 ? '' : 's',
                    $sent ? sprintf(', %d emailed', $sent) : '',
                ));

                return $this->redirect(['action' => 'index', '?' => ['survey_id' => $surveyId]]);
            }
        }
        $this->set('survey_id', $surveyId);
        $this->set('survey', $surveyId ? $this->fetchTable('Survey.Surveys')->get($surveyId) : null);
        $this->set('organisationNodes', $this->fetchTable('Organisation.OrganisationNodes')->find('list')->all());

        return null;
    }

    public function resend(string $id, ?InvitationService $invites = null): Response
    {
        $invites ??= new InvitationService();
        $invitation = $this->fetchTable('Survey.Invitations')->get($id);
        if ($invitation->used) {
            $this->Flash->error('That invitation has already been used.');
        } elseif ($invites->send($invitation)) {
            $this->Flash->success('Invitation email sent to ' . $invitation->email);
        } else {
            $this->Flash->error('Could not send email. The take-survey link is still valid.');
        }

        return $this->redirect(['action' => 'index', '?' => ['survey_id' => $invitation->survey_id]]);
    }
}
