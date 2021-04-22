<?php

namespace App\Mmg\Contracts;

interface TesterInterface
{
    /**
     * Test the given tile.
     *
     * @param TileInterface $tile
     * @return void
     */
    public function testTile($tile);
}
