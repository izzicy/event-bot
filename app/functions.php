<?php

const COMPASS_NORTH = 'COMPASS_NORTH';
const COMPASS_NORTH_EAST = 'COMPASS_NORTH_EAST';
const COMPASS_EAST = 'COMPASS_EAST';
const COMPASS_SOUTH_EAST = 'COMPASS_SOUTH_EAST';
const COMPASS_SOUTH = 'COMPASS_SOUTH';
const COMPASS_SOUTH_WEST = 'COMPASS_SOUTH_WEST';
const COMPASS_WEST = 'COMPASS_WEST';
const COMPASS_NORTH_WEST = 'COMPASS_NORTH_WEST';

if ( ! function_exists('amsterdam_distance')) {
    /**
     * A variation of the manhatten distance function that also takes into account diagonals as a step that can be taken.
     *
     * @see https://math.stackexchange.com/a/1401328
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return int
     */
    function amsterdam_distance($x1, $y1, $x2, $y2)
    {
        $xDelta = abs($x1 - $x2);
        $yDelta = abs($y1 - $y2);

        $p = max($xDelta, $yDelta);
        $q = min($xDelta, $yDelta);

        return $q * sqrt(2) + ($p - $q);
    }
}

if ( ! function_exists('align_to_compass')) {
    /**
     * Align the vector / radians to a compass.
     *
     * @param float|int $x
     * @param int|null $y
     * @return string
     */
    function align_to_compass($x, $y = null) {
        $radians = $x;
        $north = -M_PI_2;
        $northEast = -M_PI_2 + M_PI_4;
        $east = 0;
        $southEast = M_PI_2 - M_PI_4;
        $south = M_PI_2;
        $southWest = M_PI_2 + M_PI_4;
        $west = M_PI;
        $northWest = -M_PI_2 - M_PI_4;

        $m_pi_8 = M_PI_4 / 2;


        if (is_numeric($x) && is_numeric($y)) {
            $radians = atan2($y, $x);
        }

        if ($radians > $north - $m_pi_8 && $radians <= $north + $m_pi_8) {
            return COMPASS_NORTH;
        }

        if ($radians > $northEast - $m_pi_8 && $radians <= $northEast + $m_pi_8) {
            return COMPASS_NORTH_EAST;
        }

        if ($radians > $east - $m_pi_8 && $radians <= $east + $m_pi_8) {
            return COMPASS_EAST;
        }

        if ($radians > $southEast - $m_pi_8 && $radians <= $southEast + $m_pi_8) {
            return COMPASS_SOUTH_EAST;
        }

        if ($radians > $south - $m_pi_8 && $radians <= $south + $m_pi_8) {
            return COMPASS_SOUTH;
        }

        if ($radians > $southWest - $m_pi_8 && $radians <= $southWest + $m_pi_8) {
            return COMPASS_SOUTH_WEST;
        }

        if ($radians > $west - $m_pi_8 && $radians <= $west + $m_pi_8) {
            return COMPASS_WEST;
        }

        if ($radians > $northWest - $m_pi_8 && $radians <= $northWest + $m_pi_8) {
            return COMPASS_NORTH_WEST;
        }

        throw new Exception('No compass direction could be matches. This should not happen.');
    }
}

if ( ! function_exists('compass_to_radians')) {
    /**
     * Convert the compass direction to radians.
     *
     * @param string $direction
     * @return void
     */
    function compass_to_radians($direction) {
        switch ($direction) {
            case COMPASS_EAST:
                return 0;
            case COMPASS_NORTH_EAST:
                return -M_PI_4;
            case COMPASS_NORTH:
                return -M_PI_2;
            case COMPASS_NORTH_WEST:
                return -M_PI_2 - M_PI_4;
            case COMPASS_WEST:
                return M_PI;
            case COMPASS_SOUTH_WEST:
                return M_PI_2 + M_PI_4;
            case COMPASS_SOUTH;
                return M_PI_2;
            case COMPASS_SOUTH_EAST:
                return M_PI_4;
            default:
                return 0;
        }
    }
}
