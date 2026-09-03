<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class InitialSchema extends BaseMigration
{
    public function change(): void
    {
        $this->table('tenants')
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('brand_name', 'string', ['limit' => 128, 'null' => false, 'default' => 'Engagement Platform'])
            ->addColumn('support_email', 'string', ['limit' => 255, 'null' => false, 'default' => 'support@example.com'])
            ->addColumn('logo_path', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('primary_color', 'string', ['limit' => 16, 'null' => false, 'default' => '#1f4e79'])
            ->addColumn('settings', 'text', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['slug'], ['unique' => true])
            ->create();

        $this->table('roles')
            ->addColumn('tenant_id', 'integer', ['null' => true])
            ->addColumn('name', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['tenant_id', 'slug'], ['unique' => true])
            ->create();

        $this->table('users')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('organisation_node_id', 'integer', ['null' => true])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('first_name', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('is_active', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['tenant_id'])
            ->create();

        $this->table('users_roles', ['id' => false, 'primary_key' => ['user_id', 'role_id']])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('role_id', 'integer', ['null' => false])
            ->create();

        $this->table('menus')
            ->addColumn('tenant_id', 'integer', ['null' => true])
            ->addColumn('name', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->create();

        $this->table('menu_items')
            ->addColumn('menu_id', 'integer', ['null' => false])
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('label', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('url', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('role_slug', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['menu_id'])
            ->create();

        $this->table('settings')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('value', 'text', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['tenant_id', 'name'], ['unique' => true])
            ->create();

        $this->table('audits')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('model', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('foreign_key', 'integer', ['null' => true])
            ->addColumn('action', 'string', ['limit' => 32, 'null' => false])
            ->addColumn('payload', 'text', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addIndex(['tenant_id'])
            ->create();

        $this->table('organisations')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('logo_path', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['tenant_id'])
            ->create();

        $this->table('organisation_nodes')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('organisation_id', 'integer', ['null' => false])
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('lft', 'integer', ['null' => true])
            ->addColumn('rght', 'integer', ['null' => true])
            ->addColumn('depth', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('quota', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('current_respondents', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['tenant_id'])
            ->addIndex(['organisation_id'])
            ->addIndex(['lft', 'rght'])
            ->create();

        $this->table('surveys')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('organisation_id', 'integer', ['null' => false])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('kind', 'string', ['limit' => 32, 'null' => false, 'default' => 'engagement'])
            ->addColumn('status', 'string', ['limit' => 32, 'null' => false, 'default' => 'draft'])
            ->addColumn('welcome_text', 'text', ['null' => true])
            ->addColumn('thankyou_text', 'text', ['null' => true])
            ->addColumn('scale_min', 'integer', ['null' => false, 'default' => 1])
            ->addColumn('scale_max', 'integer', ['null' => false, 'default' => 5])
            ->addColumn('positive_min', 'integer', ['null' => false, 'default' => 4])
            ->addColumn('positive_max', 'integer', ['null' => false, 'default' => 5])
            ->addColumn('min_responses', 'integer', ['null' => false, 'default' => 4])
            ->addColumn('is_anonymous', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('starts', 'datetime', ['null' => true])
            ->addColumn('ends', 'datetime', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['tenant_id', 'slug'], ['unique' => true])
            ->create();

        $this->table('questions')
            ->addColumn('survey_id', 'integer', ['null' => false])
            ->addColumn('nature', 'string', ['limit' => 32, 'null' => false, 'default' => 'standard'])
            ->addColumn('type', 'string', ['limit' => 32, 'null' => false, 'default' => 'likert'])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('body', 'text', ['null' => true])
            ->addColumn('competency', 'string', ['limit' => 128, 'null' => true])
            ->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('is_required', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('is_filter', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('is_reverse_scale', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['survey_id'])
            ->create();

        $this->table('question_options')
            ->addColumn('question_id', 'integer', ['null' => false])
            ->addColumn('label', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('value', 'integer', ['null' => true])
            ->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0])
            ->create();

        $this->table('invitations')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('survey_id', 'integer', ['null' => false])
            ->addColumn('organisation_node_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('used', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['survey_id'])
            ->create();

        $this->table('responses')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('survey_id', 'integer', ['null' => false])
            ->addColumn('invitation_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('organisation_node_id', 'integer', ['null' => true])
            ->addColumn('submitted', 'datetime', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['survey_id'])
            ->create();

        $this->table('answers')
            ->addColumn('response_id', 'integer', ['null' => false])
            ->addColumn('question_id', 'integer', ['null' => false])
            ->addColumn('option_id', 'integer', ['null' => true])
            ->addColumn('numeric_value', 'integer', ['null' => true])
            ->addColumn('text_value', 'text', ['null' => true])
            ->addIndex(['response_id'])
            ->addIndex(['question_id'])
            ->create();

        $this->table('score_runs')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('survey_id', 'integer', ['null' => false])
            ->addColumn('organisation_node_id', 'integer', ['null' => true])
            ->addColumn('calculator', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('response_count', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('suppressed', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('result_json', 'text', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addIndex(['survey_id', 'calculator'])
            ->create();

        $this->table('report_templates')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('survey_id', 'integer', ['null' => true])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->create();

        $this->table('report_pages')
            ->addColumn('report_template_id', 'integer', ['null' => false])
            ->addColumn('title', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->create();

        $this->table('report_elements')
            ->addColumn('report_page_id', 'integer', ['null' => false])
            ->addColumn('type', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('config_json', 'text', ['null' => true])
            ->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0])
            ->create();

        $this->table('feedback_cycles')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('survey_id', 'integer', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 32, 'null' => false, 'default' => 'draft'])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->create();

        $this->table('rater_assignments')
            ->addColumn('feedback_cycle_id', 'integer', ['null' => false])
            ->addColumn('subject_user_id', 'integer', ['null' => false])
            ->addColumn('rater_user_id', 'integer', ['null' => false])
            ->addColumn('relationship', 'string', ['limit' => 64, 'null' => false, 'default' => 'peer'])
            ->addColumn('created', 'datetime', ['null' => true])
            ->create();

        $this->table('action_plans')
            ->addColumn('tenant_id', 'integer', ['null' => false])
            ->addColumn('survey_id', 'integer', ['null' => true])
            ->addColumn('organisation_node_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('details', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 32, 'null' => false, 'default' => 'open'])
            ->addColumn('start_date', 'date', ['null' => true])
            ->addColumn('end_date', 'date', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->create();

        $this->table('action_plan_tasks')
            ->addColumn('action_plan_id', 'integer', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('completed_date', 'date', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->create();
    }
}
