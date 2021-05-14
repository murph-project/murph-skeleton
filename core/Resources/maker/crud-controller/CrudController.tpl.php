<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Core\Controller\Admin\Crud\CrudController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Crud\Field;
use App\Core\Entity\EntityInterface;
use App\Core\Manager\EntityManager;
use <?= $entity ?> as Entity;
use <?= $factory ?> as Factory;
use <?= $form ?> as Type;
use <?= $repository_query ?> as RepositoryQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class <?= $class_name; ?> extends CrudController
{
    /**
     * @Route("/admin/<?= $route; ?>/{page}", name="admin_<?= $route; ?>_index", methods={"GET"}, requirements={"page":"\d+"})
     */
    public function index(int $page = 1, RepositoryQuery $query, Request $request, Session $session): Response
    {
        return $this->doIndex($page, $query, $request, $session);
    }

    /**
     * @Route("/admin/<?= $route; ?>/new", name="admin_<?= $route; ?>_new", methods={"GET", "POST"})
     */
    public function new(Factory $factory, EntityManager $entityManager, Request $request): Response
    {
        return $this->doNew($factory->create(), $entityManager, $request);
    }

    /**
     * @Route("/admin/<?= $route; ?>/show/{entity}", name="admin_<?= $route; ?>_show", methods={"GET"})
     */
    public function show(Entity $entity): Response
    {
        return $this->doShow($entity);
    }

    /**
     * @Route("/admin/<?= $route; ?>/filter", name="admin_<?= $route; ?>_filter", methods={"GET"})
     */
    public function filter(Session $session): Response
    {
        return $this->doFilter($session);
    }

    /**
     * @Route("/admin/<?= $route; ?>/edit/{entity}", name="admin_<?= $route; ?>_edit", methods={"GET", "POST"})
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doEdit($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/<?= $route; ?>/delete/{entity}", name="admin_<?= $route; ?>_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doDelete($entity, $entityManager, $request);
    }

    protected function getConfiguration(): CrudConfiguration
    {
        return CrudConfiguration::create()
            ->setPageTitle('index', 'List of <?= $entity; ?>')
            ->setPageTitle('edit', 'Edition of {id}')
            ->setPageTitle('new', 'New <?= $entity; ?>')
            ->setPageTitle('show', 'View of {id}')

            ->setPageRoute('index', 'admin_<?= $route; ?>_index')
            ->setPageRoute('new', 'admin_<?= $route; ?>_new')
            ->setPageRoute('edit', 'admin_<?= $route; ?>_edit')
            ->setPageRoute('show', 'admin_<?= $route; ?>_show')
            ->setPageRoute('delete', 'admin_<?= $route; ?>_delete')
            ->setPageRoute('filter', 'admin_<?= $route; ?>_filter')

            ->setForm('edit', Type::class, [])
            ->setForm('new', Type::class)
            // ->setForm('filter', Type::class)

            // ->setMaxPerPage('index', 20)

            // ->setAction('index', 'new', true)
            // ->setAction('index', 'show', true)
            // ->setAction('index', 'edit', true)
            // ->setAction('index', 'delete', true)

            // ->setAction('edit', 'back', true)
            // ->setAction('edit', 'show', true)
            // ->setAction('edit', 'delete', true)

            // ->setField('index', 'Label', Field\TextField::class, [
            //   'property' => 'label',
            // ])
        ;
    }

    protected function getSection(): string
    {
        return '<?= $route; ?>';
    }
}
