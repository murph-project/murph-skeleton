<?php

namespace App\Core\Controller\FileManager;

use App\Core\Controller\Admin\AdminController;
use App\Core\FileManager\FsFileManager;
use App\Core\Form\FileManager\DirectoryCreateType;
use App\Core\Form\FileManager\DirectoryRenameType;
use App\Core\Form\FileManager\FileInformationType;
use App\Core\Form\FileManager\FileUploadType;
use App\Core\Manager\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/file_manager")
 */
class FileManagerAdminController extends AdminController
{
    /**
     * @Route("/", name="admin_file_manager_index")
     */
    public function index(): Response
    {
        return $this->render('@Core/file_manager/index.html.twig');
    }

    /**
     * @Route("/api/directory", name="admin_file_manager_api_directory", options={"expose"=true})
     */
    public function directory(FsFileManager $manager, Request $request): Response
    {
        $files = $manager->list($request->query->get('directory', '/'));

        return $this->json($files);
    }

    /**
     * @Route("/info/{tab}/{context}", name="admin_file_manager_info", options={"expose"=true})
     */
    public function info(
        FsFileManager $manager,
        Request $request,
        EntityManager $entityManager,
        string $context = 'crud',
        string $tab = 'information'
    ): Response {
        $splInfo = $manager->getSplInfo($request->query->get('file'));

        if (!$splInfo) {
            throw $this->createNotFoundException();
        }

        $fileInfo = $manager->getFileInformation($request->query->get('file'));
        $path = $manager->getPathUri().'/'.$splInfo->getRelativePathname();

        $form = $this->createForm(FileInformationType::class, $fileInfo);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->update($fileInfo);

                $this->addFlash('success', 'The data has been saved.');
            } else {
                $this->addFlash('warning', 'The form is not valid.');
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'data-modal' => $this->generateUrl('admin_file_manager_info', [
                    'file' => $request->query->get('file'),
                    'tab' => 'attributes',
                ]),
                'path' => $splInfo->getRelativePath(),
            ]);
        }

        return $this->render('@Core/file_manager/info.html.twig', [
            'splInfo' => $splInfo,
            'path' => $path,
            'isLocked' => $manager->isLocked($splInfo->getRelativePathname()),
            'tab' => $tab,
            'form' => $form->createView(),
            'context' => $context,
        ]);
    }

    /**
     * @Route("/directory/new", name="admin_file_manager_directory_new", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function directoryNew(FsFileManager $manager, Request $request): Response
    {
        $splInfo = $manager->getSplInfo($request->query->get('file'));

        if (!$splInfo) {
            throw $this->createNotFoundException();
        }

        if (!$splInfo->isDir()) {
            throw $this->createNotFoundException();
        }

        if ($manager->isLocked($request->query->get('file'))) {
            return $this->render('@Core/file_manager/directory_new.html.twig', [
                'locked' => true,
            ]);
        }
        $form = $this->createForm(DirectoryCreateType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $status = $manager->createDirectory($form->get('name')->getData(), $request->query->get('file'));

                if (true === $status) {
                    $this->addFlash('success', 'Directory created.');
                } else {
                    $this->addFlash('warning', 'Directory not created.');
                }
            } else {
                $this->addFlash('warning', 'Unauthorized char(s).');
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'path' => $splInfo->getRelativePath(),
            ]);
        }

        return $this->render('@Core/file_manager/directory_new.html.twig', [
            'form' => $form->createView(),
            'file' => $request->query->get('file'),
            'locked' => false,
        ]);
    }

    /**
     * @Route("/directory/rename", name="admin_file_manager_directory_rename", methods={"GET", "POST"})
     */
    public function directoryRename(FsFileManager $manager, Request $request): Response
    {
        $splInfo = $manager->getSplInfo($request->query->get('file'));

        if (!$splInfo) {
            throw $this->createNotFoundException();
        }

        if (!$splInfo->isDir()) {
            throw $this->createNotFoundException();
        }

        if ($manager->isLocked($request->query->get('file'))) {
            return $this->render('@Core/file_manager/directory_rename.html.twig', [
                'locked' => true,
            ]);
        }

        $form = $this->createForm(DirectoryRenameType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $status = $manager->renameDirectory($form->get('name')->getData(), $request->query->get('file'));

                if (true === $status) {
                    $this->addFlash('success', 'Directory renamed.');
                } else {
                    $this->addFlash('warning', 'Directory not renamed.');
                }
            } else {
                $this->addFlash('warning', 'Unauthorized char(s).');
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'path' => $splInfo->getRelativePath(),
            ]);
        }

        return $this->render('@Core/file_manager/directory_rename.html.twig', [
            'form' => $form->createView(),
            'file' => $request->query->get('file'),
            'locked' => false,
        ]);
    }

    /**
     * @Route("/upload", name="admin_file_manager_upload", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function upload(FsFileManager $manager, Request $request): Response
    {
        $splInfo = $manager->getSplInfo($request->query->get('file'));

        if (!$splInfo) {
            throw $this->createNotFoundException();
        }

        if (!$splInfo->isDir()) {
            throw $this->createAccessDeniedException();
        }

        if ($manager->isLocked($request->query->get('file'))) {
            return $this->render('@Core/file_manager/upload.html.twig', [
                'locked' => true,
            ]);
        }
        $form = $this->createForm(FileUploadType::class, null, [
            'mimes' => $manager->getMimes(),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $manager->upload($form->get('files')->getData(), $request->query->get('file'));

                $this->addFlash('success', 'Files uploaded.');
            } else {
                $this->addFlash('warning', 'Unauthorized file type(s).');
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'path' => $splInfo->getRelativePathname(),
            ]);
        }

        return $this->render('@Core/file_manager/upload.html.twig', [
            'form' => $form->createView(),
            'file' => $request->query->get('file'),
            'locked' => false,
        ]);
    }

    /**
     * @Route("/delete", name="admin_file_manager_delete", methods={"DELETE"})
     */
    public function delete(FsFileManager $manager, Request $request): Response
    {
        $path = $request->request->get('file');
        $splInfo = $manager->getSplInfo($request->request->get('file'));

        if (!$splInfo) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            if ($manager->delete($path)) {
                $this->addFlash('success', 'The data has been removed.');
            } else {
                $this->addFlash('warning', 'The data has not been removed.');
            }
        }

        return $this->redirectToRoute('admin_file_manager_index', [
            'path' => $splInfo->getRelativePath(),
        ]);
    }

    protected function getSection(): string
    {
        return 'file_manager';
    }
}
