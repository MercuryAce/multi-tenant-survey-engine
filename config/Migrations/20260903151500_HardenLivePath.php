<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class HardenLivePath extends BaseMigration
{
    public function change(): void
    {
        $this->table('invitations')
            ->addColumn('sent_at', 'datetime', ['null' => true])
            ->addColumn('sent_count', 'integer', ['null' => false, 'default' => 0])
            ->update();

        $this->table('rater_assignments')
            ->addColumn('invitation_id', 'integer', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->update();
    }
}
