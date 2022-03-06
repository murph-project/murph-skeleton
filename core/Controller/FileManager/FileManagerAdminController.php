<?php

namespace App\Core\Controller\FileManager;

use App\Core\Controller\Admin\AdminController;
use App\Core\FileManager\FsFileManager;
use App\Core\Form\FileManager\DirectoryCreateType;
use App\Core\Form\FileManager\DirectoryRenameType;
use App\Core\Form\FileManager\FileInformationType;
use App\Core\Form\FileManager\FileRenameType;
use App\Core\Form\FileManager\FileUploadType;
use App\Core\Manager\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $options = [
            'sort' => $request->query->get('_sort', 'name'),
            'sort_direction' => $request->query->get('_sort_direction', 'asc'),
        ];

        $files = $manager->list($request->query->get('directory', '/'), $options);

        return $this->json($files);
    }

    /**
     * @Route("/info/{tab}/{context}/{ajax}", name="admin_file_manager_info", options={"expose"=true})
     */
    public function info(
        FsFileManager $manager,
        Request $request,
        EntityManager $entityManager,
        TranslatorInterface $translator,
        string $context = 'crud',
        string $tab = 'information',
        bool $ajax = false
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

                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('success', 'The data has been saved.');
                } else {
                    return $this->json([
                        '_error' => 0,
                        '_message' => $translator->trans('The data has been saved.'),
                        '_level' => 'success',
                        '_dispatch' => 'file_manager.info.update.success',
                    ]);
                }
            } else {
                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('warning', 'The form is not valid.');
                } else {
                    return $this->json([
                        '_error' => 1,
                        '_message' => $translator->trans('The form is not valid.'),
                        '_level' => 'warning',
                        '_dispatch' => 'file_manager.info.update.error',
                    ]);
                }
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
            'ajax' => $ajax,
        ]);
    }

    /**
     * @Route("/directory/new/{ajax}", name="admin_file_manager_directory_new", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function directoryNew(FsFileManager $manager, Request $request, TranslatorInterface $translator, bool $ajax = false): Response
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
                    if (!$request->isXmlHttpRequest()) {
                        $this->addFlash('success', 'Directory created.');
                    } else {
                        return $this->json([
                            '_error' => 0,
                            '_message' => $translator->trans('Directory created.'),
                            '_level' => 'success',
                            '_dispatch' => 'file_manager.directory.new.success',
                        ]);
                    }
                } else {
                    if (!$request->isXmlHttpRequest()) {
                        $this->addFlash('warning', 'Directory not created.');
                    } else {
                        return $this->json([
                            '_error' => 1,
                            '_message' => $translator->trans('Directory not created.'),
                            '_level' => 'warning',
                            '_dispatch' => 'file_manager.directory.new.error',
                        ]);
                    }
                }
            } else {
                $this->addFlash('warning', 'Unauthorized char(s).');
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'path' => $splInfo->getRelativePathname(),
            ]);
        }

        return $this->render('@Core/file_manager/directory_new.html.twig', [
            'form' => $form->createView(),
            'file' => $request->query->get('file'),
            'ajax' => $ajax,
            'locked' => false,
        ]);
    }

    /**
     * @Route("/directory/rename/{ajax}", name="admin_file_manager_directory_rename", methods={"GET", "POST"})
     */
    public function directoryRename(FsFileManager $manager, Request $request, TranslatorInterface $translator, bool $ajax = false): Response
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

        $form = $this->createForm(DirectoryRenameType::class, [
            'name' => $splInfo->getFilename(),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $status = $manager->renameDirectory($form->get('name')->getData(), $request->query->get('file'));

                if (true === $status) {
                    if (!$request->isXmlHttpRequest()) {
                        $this->addFlash('success', 'Directory renamed.');
                    } else {
                        return $this->json([
                            '_error' => 0,
                            '_message' => $translator->trans('Directory renamed.'),
                            '_level' => 'success',
                            '_dispatch' => 'file_manager.directory.rename.success',
                        ]);
                    }
                } else {
                    if (!$request->isXmlHttpRequest()) {
                        $this->addFlash('warning', 'Directory not renamed.');
                    } else {
                        return $this->json([
                            '_error' => 1,
                            '_message' => $translator->trans('Directory not renamed.'),
                            '_level' => 'warning',
                            '_dispatch' => 'file_manager.directory.rename.error',
                        ]);
                    }
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
            'ajax' => $ajax,
        ]);
    }

    /**
     * @Route("/file/rename/{ajax}", name="admin_file_manager_file_rename", methods={"GET", "POST"})
     */
    public function fileRename(FsFileManager $manager, Request $request, TranslatorInterface $translator, bool $ajax = false): Response
    {
        $splInfo = $manager->getSplInfo($request->query->get('file'));

        if (!$splInfo) {
            throw $this->createNotFoundException();
        }

        if ($splInfo->isDir()) {
            throw $this->createNotFoundException();
        }

        if ($manager->isLocked($request->query->get('file'))) {
            return $this->render('@Core/file_manager/file_rename.html.twig', [
                'locked' => true,
            ]);
        }

        $form = $this->createForm(FileRenameType::class, [
            'name' => preg_replace(sprintf('/\.%s/', $splInfo->getExtension()), '', $splInfo->getFilename()),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $status = $manager->renameFile($form->get('name')->getData(), $request->query->get('file'));

                if (true === $status) {
                    if (!$request->isXmlHttpRequest()) {
                        $this->addFlash('success', 'File renamed.');
                    } else {
                        return $this->json([
                            '_error' => 0,
                            '_message' => $translator->trans('File renamed.'),
                            '_level' => 'success',
                            '_dispatch' => 'file_manager.file.rename.success',
                        ]);
                    }
                } else {
                    if (!$request->isXmlHttpRequest()) {
                        $this->addFlash('warning', 'File not renamed.');
                    } else {
                        return $this->json([
                            '_error' => 1,
                            '_message' => $translator->trans('File not renamed.'),
                            '_level' => 'warning',
                            '_dispatch' => 'file_manager.file.rename.error',
                        ]);
                    }
                }
            } else {
                $this->addFlash('warning', 'Unauthorized char(s).');
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'path' => $splInfo->getRelativePath(),
            ]);
        }

        return $this->render('@Core/file_manager/file_rename.html.twig', [
            'form' => $form->createView(),
            'file' => $request->query->get('file'),
            'exention' => $splInfo->getExtension(),
            'locked' => false,
            'ajax' => $ajax,
        ]);
    }

    /**
     * @Route("/upload/{ajax}", name="admin_file_manager_upload", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function upload(FsFileManager $manager, Request $request, TranslatorInterface $translator, bool $ajax = false): Response
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
                if ($form->get('files')->getData()) {
                    $manager->upload(
                        $form->get('files')->getData(),
                        $request->query->get('file')
                    );
                }

                if ($form->get('directory')->getData()) {
                    $manager->upload(
                        $form->get('directory')->getData(),
                        $request->query->get('file'),
                        $_FILES['file_upload']['full_path']['directory'] ?? []
                    );
                }

                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('success', 'Files uploaded.');
                } else {
                    return $this->json([
                        '_error' => 0,
                        '_message' => $translator->trans('Files uploaded.'),
                        '_level' => 'success',
                        '_dispatch' => 'file_manager.file.new.success',
                    ]);
                }
            } else {
                if (!$request->isXmlHttpRequest()) {
                    $this->addFlash('warning', 'Unauthorized file type(s).');
                } else {
                    return $this->json([
                        '_error' => 1,
                        '_message' => $translator->trans('Unauthorized file type(s).'),
                        '_level' => 'warning',
                        '_dispatch' => 'file_manager.file.new.error',
                    ]);
                }
            }

            return $this->redirectToRoute('admin_file_manager_index', [
                'path' => $splInfo->getRelativePathname(),
            ]);
        }

        return $this->render('@Core/file_manager/upload.html.twig', [
            'form' => $form->createView(),
            'file' => $request->query->get('file'),
            'locked' => false,
            'ajax' => $ajax,
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
