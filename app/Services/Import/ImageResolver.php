<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageResolver
{
    /** @var array<string, string> Optional lookup map of lowercase filename -> absolute path */
    protected array $lookup = [];

    /** @var string[] Supported image extensions in priority order */
    protected array $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.avif', '.gif'];

    /** @var string The directory to search for source images */
    protected string $searchDirectory;

    /** @var string The storage path to copy images to */
    protected string $storagePath;

    public function __construct()
    {
        $this->searchDirectory = config('import.image_directory', storage_path('app/public/import_images'));
        $this->storagePath = config('import.image_storage_path', 'products');
    }

    /**
     * Set the in-memory lookup map for images.
     *
     * @param array<string, string> $lookup
     */
    public function setLookup(array $lookup): void
    {
        $this->lookup = $lookup;
    }

    /**
     * Resolve an image name (without extension) to a file path.
     * Searches the configured directory for a matching file using the priority extension list.
     *
     * @param string $imageName Image name without extension
     * @return string|null The relative path within the public disk, or null if not found
     */
    public function resolve(string $imageName): ?string
    {
        $normalizedName = $this->normalizeImageValue($imageName);

        if ($normalizedName === '') {
            return null;
        }

        $candidateNames = [$normalizedName];
        $pathInfo = pathinfo($normalizedName);
        if (!empty($pathInfo['filename']) && strtolower($pathInfo['filename']) !== strtolower($normalizedName)) {
            $candidateNames[] = $pathInfo['filename'];
        }

        $candidates = [];
        foreach ($candidateNames as $candidateName) {
            $extension = pathinfo($candidateName, PATHINFO_EXTENSION);
            if ($extension !== '') {
                $candidates[] = $candidateName;
                continue;
            }

            foreach ($this->extensions as $ext) {
                $candidates[] = $candidateName . $ext;
            }
        }

        $candidates = array_values(array_unique($candidates));

        foreach ($candidates as $candidateName) {
            if (!empty($this->lookup)) {
                $key = strtolower($candidateName);
                if (isset($this->lookup[$key])) {
                    $filePath = $this->lookup[$key];
                    if (File::exists($filePath)) {
                        $destFilename = $this->buildDestinationFilename($normalizedName, pathinfo($filePath, PATHINFO_EXTENSION));
                        $destPath = $this->storagePath . '/' . $destFilename;

                        Storage::disk('public')->put($destPath, File::get($filePath));

                        return $destPath;
                    }
                }
            }

            foreach (File::files($this->searchDirectory) as $file) {
                if (strtolower($file->getFilename()) === strtolower($candidateName)) {
                    $destFilename = $this->buildDestinationFilename($normalizedName, $file->getExtension());
                    $destPath = $this->storagePath . '/' . $destFilename;

                    Storage::disk('public')->put($destPath, File::get($file->getRealPath()));

                    return $destPath;
                }
            }
        }

        return null;
    }

    protected function normalizeImageValue(string $imageName): string
    {
        $trimmed = trim($imageName);

        if ($trimmed === '') {
            return '';
        }

        return ltrim(basename(str_replace('\\', '/', $trimmed)), '/');
    }

    protected function buildDestinationFilename(string $requestedName, string $foundExtension): string
    {
        $requestedExtension = pathinfo($requestedName, PATHINFO_EXTENSION);

        if ($requestedExtension !== '') {
            return $requestedName;
        }

        return $requestedName . '.' . strtolower($foundExtension);
    }

    /**
     * Resolve multiple comma-separated image names.
     *
     * @param string|null $imageNames Comma-separated image names
     * @return array<string> Array of resolved storage paths
     */
    public function resolveMultiple(?string $imageNames): array
    {
        if (empty($imageNames)) {
            return [];
        }

        $resolved = [];
        $names = array_map('trim', explode(',', $imageNames));

        foreach ($names as $name) {
            if (empty($name)) {
                continue;
            }

            $path = $this->resolve($name);
            if ($path !== null) {
                $resolved[] = $path;
            }
        }

        return $resolved;
    }
}
