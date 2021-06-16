<?php

namespace App\Core\Controller\Task;

use App\Core\Controller\Admin\AdminController;
use App\Core\Event\Task\TaskInitEvent;
use App\Core\Event\Task\TaskRunRequestedEvent;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/task")
 */
class TaskAdminController extends AdminController
{
    /**
     * @Route("/", name="admin_task_index")
     */
    public function index(EventDispatcherInterface $eventDispatcher): Response
    {
        $event = new TaskInitEvent();
        $eventDispatcher->dispatch($event, TaskInitEvent::INIT_EVENT);

        return $this->render('@Core/task/task_admin/index.html.twig', [
            'pager' => $event->getTasks(),
        ]);
    }

    /**
     * @Route("/run/{task}", name="admin_task_run", methods={"GET"})
     */
    public function run(
        string $task,
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        if (!$this->isCsrfTokenValid('task_run', $request->query->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $output = new BufferedOutput();
        $event = new TaskRunRequestedEvent($task, $request->query, $output);
        $eventDispatcher->dispatch($event, TaskRunRequestedEvent::RUN_REQUEST_EVENT);

        $converter = new AnsiToHtmlConverter(new SolarizedTheme());
        $content = $converter->convert($output->fetch());

        return $this->render('@Core/task/task_admin/run.html.twig', [
            'output' => $content,
        ]);
    }

    public function getSection(): string
    {
        return 'task';
    }
}
