<?php
declare(strict_types=1);

namespace Survey\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Security;

class InvitationsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('invitations');
        $this->addBehavior('Timestamp');
        $this->addBehavior('AdminKit.TenantScope');
        $this->belongsTo('Surveys', ['className' => 'Survey.Surveys']);
        $this->belongsTo('OrganisationNodes', ['className' => 'Organisation.OrganisationNodes']);
        $this->belongsTo('Users', ['className' => 'AdminKit.Users']);
    }

    public function issue(int $surveyId, ?int $nodeId, ?string $email, ?int $userId = null): \Cake\Datasource\EntityInterface
    {
        $entity = $this->newEntity([
            'survey_id' => $surveyId,
            'organisation_node_id' => $nodeId,
            'user_id' => $userId,
            'email' => $email,
            'token' => bin2hex(Security::randomBytes(16)),
            'used' => false,
            'sent_count' => 0,
        ]);

        return $this->saveOrFail($entity);
    }
}
