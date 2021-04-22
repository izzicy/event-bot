<?php

namespace App\Mmg\Testers;

use App\Mmg\Contracts\TesterInterface;

class AggregateTester implements TesterInterface
{
    /** @var TesterInterface[] */
    protected $testers;

    /**
     * @param TesterInterface[] $testers
     */
    public function __construct($testers)
    {
        $this->testers = $testers;
    }

    /** @inheritDoc */
    public function testTile($tile)
    {
        foreach ($this->testers as $tester) {
            $tester->testTile($tile);
        }
    }
}
