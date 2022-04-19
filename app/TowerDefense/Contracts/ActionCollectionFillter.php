<?php

namespace App\TowerDefense\Contracts;

use App\TowerDefense\Actions\ActionCollection;

interface ActionCollectionFiller
{
    /**
     * Fill the action collection.
     *
     * @param ActionCollection $actions
     * @return ActionCollection
     */
    public function fill(ActionCollection $actions): ActionCollection;
}
