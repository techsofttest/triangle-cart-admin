<?php

namespace App\Services\Import;

use App\Models\Category;

class CategoryResolver
{
    /** @var array<string, Category> Parent categories keyed by normalized lowercase name */
    protected array $parentCache = [];

    /** @var array<string, Category> Subcategories keyed by "parentId:normalizedLowercaseName" */
    protected array $childCache = [];

    /**
     * Pre-load all existing categories into the cache.
     */
    public function warmCache(): void
    {
        foreach (Category::all() as $category) {
            $key = $this->normalizeName($category->name);

            if (is_null($category->parent_id)) {
                $this->parentCache[$key] = $category;
            } else {
                $childKey = $category->parent_id . ':' . $key;
                $this->childCache[$childKey] = $category;
            }
        }
    }

    /**
     * Resolve a parent category by name. Creates if missing.
     *
     * @param string $name
     * @return Category
     */
    public function resolveParent(string $name): Category
    {
        $normalized = $this->normalizeName($name);

        if (isset($this->parentCache[$normalized])) {
            return $this->parentCache[$normalized];
        }

        $category = Category::whereNull('parent_id')
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->first();

        if (!$category) {
            $category = Category::create([
                'name' => trim($name),
                'is_active' => true,
            ]);
        }

        $this->parentCache[$normalized] = $category;

        return $category;
    }

    /**
     * Resolve a subcategory by name under a given parent. Creates if missing.
     *
     * @param string $name
     * @param Category $parent
     * @return Category
     */
    public function resolveChild(string $name, Category $parent): Category
    {
        $normalized = $this->normalizeName($name);
        $childKey = $parent->id . ':' . $normalized;

        if (isset($this->childCache[$childKey])) {
            return $this->childCache[$childKey];
        }

        $category = Category::where('parent_id', $parent->id)
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->first();

        if (!$category) {
            $category = Category::create([
                'name' => trim($name),
                'parent_id' => $parent->id,
                'is_active' => true,
            ]);
        }

        $this->childCache[$childKey] = $category;

        return $category;
    }

    protected function normalizeName(string $name): string
    {
        return mb_strtolower(trim($name));
    }
}
