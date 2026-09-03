<?php
declare(strict_types=1);

namespace ActionPlanning\Controller;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\I18n\Date;

class ActionPlansController extends AppController
{
    public function index(): void
    {
        $plans = $this->fetchTable('ActionPlanning.ActionPlans')
            ->find()
            ->contain(['ActionPlanTasks', 'Surveys', 'Users'])
            ->all();
        $this->set(compact('plans'));
    }

    public function add(): ?Response
    {
        $table = $this->fetchTable('ActionPlanning.ActionPlans');
        $plan = $table->newEmptyEntity();
        if ($this->request->is('post')) {
            $plan = $table->patchEntity($plan, $this->request->getData());
            if ($table->save($plan)) {
                $this->Flash->success('Action plan saved.');

                return $this->redirect(['action' => 'view', $plan->id]);
            }
        }
        $this->set('surveys', $this->fetchTable('Survey.Surveys')->find('list')->all());
        $this->set('users', $this->fetchTable('AdminKit.Users')->find('list', valueField: function ($u) {
            return trim($u->first_name . ' ' . $u->last_name) . ' (' . $u->email . ')';
        })->all());
        $this->set(compact('plan'));

        return null;
    }

    public function view(string $id): ?Response
    {
        $plan = $this->plan((int)$id);
        if ($this->request->is('post')) {
            $tasks = $this->fetchTable('ActionPlanning.ActionPlanTasks');
            $tasks->save($tasks->newEntity([
                'action_plan_id' => $plan->id,
                'name' => $this->request->getData('name'),
            ]));
            $this->Flash->success('Task added.');

            return $this->redirect(['action' => 'view', $id]);
        }
        $this->set(compact('plan'));
        $this->set('progress', $this->progress($plan));

        return null;
    }

    public function completeTask(string $id): Response
    {
        $this->request->allowMethod(['post']);
        $tasks = $this->fetchTable('ActionPlanning.ActionPlanTasks');
        $task = $tasks->get($id);
        $task->completed_date = Date::today();
        $tasks->saveOrFail($task);
        $this->Flash->success('Task marked complete.');

        return $this->redirect(['action' => 'view', $task->action_plan_id]);
    }

    public function reopenTask(string $id): Response
    {
        $this->request->allowMethod(['post']);
        $tasks = $this->fetchTable('ActionPlanning.ActionPlanTasks');
        $task = $tasks->get($id);
        $task->completed_date = null;
        $tasks->saveOrFail($task);
        $this->Flash->success('Task reopened.');

        return $this->redirect(['action' => 'view', $task->action_plan_id]);
    }

    public function status(string $id): Response
    {
        $this->request->allowMethod(['post']);
        $plan = $this->plan((int)$id);
        $status = (string)$this->request->getData('status');
        if (!in_array($status, ['open', 'done', 'paused'], true)) {
            $this->Flash->error('Unknown status.');

            return $this->redirect(['action' => 'view', $id]);
        }
        $plan->status = $status;
        $this->fetchTable('ActionPlanning.ActionPlans')->saveOrFail($plan);
        $this->Flash->success('Plan status updated.');

        return $this->redirect(['action' => 'view', $id]);
    }

    private function plan(int $id): object
    {
        return $this->fetchTable('ActionPlanning.ActionPlans')->get($id, contain: ['ActionPlanTasks', 'Surveys', 'Users']);
    }

    /**
     * @return array{total:int,done:int,percent:int}
     */
    private function progress(object $plan): array
    {
        $total = count($plan->action_plan_tasks);
        $done = 0;
        foreach ($plan->action_plan_tasks as $task) {
            if ($task->completed_date) {
                $done++;
            }
        }

        return [
            'total' => $total,
            'done' => $done,
            'percent' => $total ? (int)round(100 * $done / $total) : 0,
        ];
    }
}
