<?php

namespace App\Core\FileManager;

use App\Core\Entity\FileInformation;
use App\Core\Factory\FileInformationFactory;
use App\Core\Form\FileUploadHandler;
use App\Core\Repository\FileInformationRepositoryQuery;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function Symfony\Component\String\u;

/**
 * class FsFileManager.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class FsFileManager
{
    protected array $mimes;
    protected string $path;
    protected string $pathUri;
    protected array $pathLocked;
    protected FileUploadHandler $uploadHandler;
    protected FileInformationFactory $fileInformationFactory;
    protected FileInformationRepositoryQuery $fileInformationRepositoryQuery;

    public function __construct(
        ParameterBagInterface $params,
        FileUploadHandler $uploadHandler,
        FileInformationFactory $fileInformationFactory,
        FileInformationRepositoryQuery $fileInformationRepositoryQuery
    ) {
        $config = $params->get('core')['file_manager'];

        $this->uploadHandler = $uploadHandler;
        $this->fileInformationFactory = $fileInformationFactory;
        $this->fileInformationRepositoryQuery = $fileInformationRepositoryQuery;

        $this->mimes = $config['mimes'];
        $this->path = $config['path'];
        $this->pathUri = $this->normalizePath($config['path_uri']);

        foreach ($config['path_locked'] as $k => $v) {
            $config['path_locked'][$k] = sprintf('/%s/', $this->normalizePath($v));
        }

        $this->pathLocked = $config['path_locked'];
    }

    public function list(string $directory, array $options = []): array
    {
        $directory = $this->normalizePath($directory);

        $breadcrumb = ['/'];

        if ($directory) {
            $breadcrumb = array_merge(
                $breadcrumb,
                explode('/', $directory)
            );
        }

        $data = [
            'breadcrumb' => $breadcrumb,
            'parent' => dirname($directory),
            'directories' => [],
            'files' => [],
        ];

        $finder = new Finder();
        $finder->directories()->depth('== 0')->in($this->path.'/'.$directory);

        $this->applySort($finder, $options['sort'] ?? 'name', $options['sort_direction'] ?? 'asc');

        foreach ($finder as $file) {
            $data['directories'][] = [
                'basename' => $file->getBasename(),
                'path' => $directory.'/'.$file->getBasename(),
                'webPath' => $this->pathUri.'/'.$directory.'/'.$file->getBasename(),
                'locked' => $this->isLocked($directory.'/'.$file->getBasename()),
                'mime' => null,
            ];
        }

        $finder = new Finder();
        $finder->files()->depth('== 0')->in($this->path.'/'.$directory);

        $this->applySort($finder, $options['sort'] ?? 'name', $options['sort_direction'] ?? 'asc');

        foreach ($finder as $file) {
            $data['files'][] = [
                'basename' => $file->getBasename(),
                'path' => $directory,
                'webPath' => $this->pathUri.'/'.$directory.'/'.$file->getBasename(),
                'locked' => $this->isLocked($directory.'/'.$file->getBasename()),
                'mime' => mime_content_type($file->getRealPath()),
            ];
        }

        return $data;
    }

    public function getSplInfo(string $path): ?SplFileInfo
    {
        $path = $this->normalizePath($path);

        if ('' === $path) {
            return new SplFileInfo($this->path, '', '');
        }

        $finder = new Finder();
        $finder->in($this->path)
            ->depth('== '.substr_count($path, '/'))
            ->name(basename($path))
        ;

        $dirname = dirname($path);

        if ('.' === $dirname) {
            $dirname = '';
        }

        foreach ($finder as $file) {
            if ($file->getRelativePath() === $dirname) {
                return $file;
            }
        }

        return null;
    }

    public function getFileInformation(string $path): ?FileInformation
    {
        $file = $this->getSplInfo($path);

        if (!$file) {
            return null;
        }

        if ($file->isDir()) {
            return null;
        }

        $hash = hash_file('sha384', $file->getPathName());

        $info = $this->fileInformationRepositoryQuery
            ->where('.id = :hash')
            ->setParameter(':hash', $hash)
            ->findOne()
        ;

        if (!$info) {
            $info = $this->fileInformationFactory->create($hash);
        }

        return $info;
    }

    public function createDirectory(string $name, string $path): bool
    {
        $file = $this->getSplInfo($path);

        if (!$file || $this->isLocked($path)) {
            return false;
        }

        $filesystem = new Filesystem();
        $path = $file->getPathname().'/'.$this->normalizePath($name);

        if ($filesystem->exists($path)) {
            return false;
        }

        $filesystem->mkdir($path, 0755);

        return true;
    }

    public function renameDirectory(string $name, string $path): bool
    {
        $file = $this->getSplInfo($path);

        if (!$file || $this->isLocked($path)) {
            return false;
        }

        $filesystem = new Filesystem();
        $newPath = $file->getPath().'/'.$this->normalizePath($name);

        if ($filesystem->exists($newPath)) {
            return false;
        }

        $filesystem->rename($file->getPathName(), $newPath);

        return true;
    }

    public function renameFile(string $name, string $path, bool $keepExtension = true): bool
    {
        $file = $this->getSplInfo($path);

        if (!$file || $this->isLocked($path)) {
            return false;
        }

        $filesystem = new Filesystem();
        $newPath = $file->getPath().'/'.$this->normalizePath($name);

        if ($keepExtension && $file->getExtension()) {
            $newPath .= sprintf('.%s', $file->getExtension());
        }

        if ($filesystem->exists($newPath)) {
            return false;
        }

        $filesystem->rename($file->getPathName(), $newPath);

        return true;
    }

    public function upload($files, string $path, array $fullPaths = [])
    {
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        foreach ($files as $key => $file) {
            $directory = $this->path.'/'.$path;

            if (isset($fullPaths[$key])) {
                $directory .= '/'.trim(dirname($fullPaths[$key]), '/');
            }

            $this->uploadHandler->handleForm($file, $directory, null, true);
        }
    }

    public function delete(string $path): bool
    {
        $file = $this->getSplInfo($path);

        if ($this->isLocked($file)) {
            return false;
        }

        if ($file) {
            $filesystem = new Filesystem();
            $filesystem->remove($file);

            return true;
        }

        return false;
    }

    public function isLocked($path): bool
    {
        $file = $this->getSplInfo($path);

        if (!$file) {
            return false;
        }

        foreach ($this->pathLocked as $lock) {
            if (u($file->getPathName().'/')->startsWith($lock)) {
                return true;
            }
        }

        return false;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPathUri(): string
    {
        return $this->pathUri;
    }

    public function getMimes(): array
    {
        return $this->mimes;
    }

    public function getPathLocked(): array
    {
        return $this->pathLocked;
    }

    protected function applySort(Finder $finder, string $sort, string $direction)
    {
        if ('name' === $sort) {
            $finder->sortByName();
        } elseif ('modification_date' === $sort) {
            $finder->sortByModifiedTime();
        }

        if ('desc' === $direction) {
            $finder->reverseSorting();
        }
    }

    protected function normalizePath(string $path): string
    {
        return (string) u($path)
            ->replace('..', '.')
            ->replaceMatches('#/{2,}#', '/')
            ->replaceMatches('#^.$#', '')
            ->trim('/')
            ->trim()
        ;
    }
}
