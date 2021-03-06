<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;

class IssueFocus extends SelectFilter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request               $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed                                  $value
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query;
    }

    /**
     * Apply the filter to the given jira options.
     *
     * @param  array  $options
     * @param  mixed  $value
     *
     * @return void
     */
    public function applyToJiraOptions(&$options, $value)
    {
        $options['groups'] = [
            'dev' => $value == 'dev' || is_null($value),
            'ticket' => $value == 'ticket' || is_null($value),
            'other' => true
        ];
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function options(Request $request)
    {
        return array_flip([
            'dev' => 'Dev',
            'ticket' => 'Ticket'
        ]);
    }

    /**
     * Returns the default options for the filter.
     *
     * @return array|mixed
     */
    public function default()
    {
        return 'dev';
    }
}
