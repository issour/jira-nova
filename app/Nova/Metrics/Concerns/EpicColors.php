<?php

namespace App\Nova\Metrics\Concerns;

use App\Models\Issue;

trait EpicColors
{
    /**
     * Assigns epic colors to the specified result.
     *
     * @param  mixed  $result
     *
     * @return mixed
     */
    public function assignEpicColors($result)
    {
        // Determine the epic colors
        $colors = $this->getEpicColors(array_keys($result->value));

        // Assign the colors
        $result->colors($colors);

        // Return the result
        return $result;
    }

    /**
     * Returns the epic colors.
     *
     * @param  array  $epics
     *
     * @return array
     */
    public function getEpicColors($epics)
    {
        // Determine the epic colors
        $colors = Issue::getEpicColors();

        // Reduce the color list to only present values
        $colors = collect($colors)->only($epics);

        // Initialize the color counts
        $counts = $colors->flip()->map(function($color) {
            return 0;
        });

        // If a color is repeated, force a different color
        $colors->transform(function($color, $epic) use (&$counts) {

            // Increase the color count
            $counts[$color] = $counts[$color] + 1;

            // If this is the first of its kind, keep it
            if($counts[$color] == 1) {
                return $color;
            }

            // Detemrine the count
            $count = $counts[$color];

            // Offset each digit
            return '#' . implode('', array_map(function($v) use ($count) {
                return dechex((hexdec($v) - $count + 16) % 16);
            }, str_split(substr($color, 1))));

        });

        // Return the colors
        return $colors->all();
    }
}