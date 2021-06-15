<?php

namespace App\Core\Controller\FileManager;

use App\Core\Controller\Admin\AdminController;
use App\Core\FileManager\FsFileManager;
use App\Core\Form\FileManager\DirectoryCreateType;
use App\Core\Form\FileManager\DirectoryRenameType;
use App\Core\Form\FileManager\FileUploadType;
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
     * @Route("/info", name="admin_file_manager_info", options={"expose"=true})
     */
    public function info(FsFileManager $manager, Request $request): Response
    {
        $info = $manager->info($request->query->get('file'));

        if (!$info) {
            throw $this->createNotFoundException();
        }

        $path = $manager->getPathUri().'/'.$info->getRelativePathname();

        return $this->render('@Core/file_manager/info.html.twig', [
            'info' => $info,
            'path' => $path,
            'isLocked' => $manager->isLocked($info->getRelativePathname()),
        ]);
    }

    /**
     * @Route("/directory/new", name="admin_file_manager_directory_new", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function directoryNew(FsFileManager $manager, Request $request): Response
    {
        $info = $manager->info($request->query->get('file'));

        if (!$info) {
            throw $this->createNotFoundException();
        }

        if (!$info->isDir()) {
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
                'path' => $info->getRelativePath(),
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
        $info = $manager->info($request->query->get('file'));

        if (!$info) {
            throw $this->createNotFoundException();
        }

        if (!$info->isDir()) {
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
                'path' => $info->getRelativePath(),
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
        $info = $manager->info($request->query->get('file'));

        if (!$info) {
            throw $this->createNotFoundException();
        }

        if (!$info->isDir()) {
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
                'path' => $info->getRelativePathname(),
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
        $info = $manager->info($request->request->get('file'));

        if (!$info) {
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
            'path' => $info->getRelativePath(),
        ]);
    }

    protected function getSection(): string
    {
        return 'file_manager';
    }
}
