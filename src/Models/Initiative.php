<?php

/**
 * Initiative Model
 * Global Harmony Initiative Website
 */

namespace GHI\Models;

class Initiative extends BaseModel
{
    protected string $table = 'initiatives';

    /**
     * Get initiatives by cause
     */
    public function getByCause(int $causeId, int $limit = 0): array
    {
        $conditions = ['cause_id' => $causeId, 'status' => 'published'];
        $orderBy = 'created_at DESC';

        return $this->all($conditions, $orderBy, $limit);
    }

    /**
     * Get initiatives by core objective
     */
    public function getByObjective(string $category): array
    {
        $conditions = ['category' => $category, 'status' => 'published'];
        $orderBy = 'created_at DESC';

        return $this->all($conditions, $orderBy);
    }
}
