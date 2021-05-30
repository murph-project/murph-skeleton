<?php

namespace App\Core\Event\Task;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * class TaskInitEvent.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class TaskInitEvent extends Event
{
    const INIT_EVENT = 'task_event.init';

    protected array $tasks = [];

    public function getTasks(): array
    {
        usort($this->tasks, function ($t1, $t2) {
            return $t1['section'] != $t2['section'];
        });

        return $this->tasks;
    }

    public function addTask(string $task, string $section, string $label): self
    {
        $this->tasks[$task] = [
            'label' => $label,
            'section' => $section,
            'task' => $task,
        ];

        return $this;
    }
}
