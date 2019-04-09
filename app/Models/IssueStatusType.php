<?php

namespace App\Models;

use JiraRestApi\Issue\IssueStatus as JiraIssueStatus;

class IssueStatusType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jira_id'
    ];

    /**
     * Creates or updates the specified issue status type from jira.
     *
     * @param  \JiraRestApi\Issue\IssueStatus  $jira
     * @param  array                           $options
     *
     * @return static
     */
    public static function createOrUpdateFromJira(JiraIssueStatus $jira, $options = [])
    {
        // Try to find the existing issue status type in our system
        if(!is_null($statusType = static::where('jira_id', '=', $jira->id)->first())) {

            // Update the issue status type
            return $statusType->updateFromJira($jira, $options);

        }

        // Create the issue status type
        return static::createFromJira($jira, $options);
    }

    /**
     * Creates a new issue status type from the specified jira issue status type.
     *
     * @param  \JiraRestApi\Issue\IssueStatus  $jira
     * @param  array                           $options
     *
     * @return static
     */
    public static function createFromJira(JiraIssueStatus $jira, $options = [])
    {
        // Create a new issue status type
        $statusType = new static;

        // Update the issue status type from jira
        return $statusType->updateFromJira($jira, $options);
    }

    /**
     * Syncs this model from jira.
     *
     * @param  \JiraRestApi\Issue\IssueStatus  $jira
     * @param  array                           $options
     *
     * @return $this
     */
    public function updateFromJira(JiraIssueStatus $jira, $options = [])
    {
        // Perform all actions within a transaction
        return $this->getConnection()->transaction(function() use ($jira, $options) {

            // If a project was provided, associate it
            if(!is_null($project = ($options['project'] ?? null))) {
                $this->project()->associate($project);
            }

            // If a category was provided, associate it
            if(!is_null($category = ($options['category'] ?? null))) {
                $this->category()->associate($category);
            }

            // Assign the attributes
            $this->jira_id = $jira->id;
            $this->display_name = $jira->name;
            $this->icon_url = $jira->iconUrl;
            $this->description = $jira->description;

            // Save
            $this->save();

            // Allow chaining
            return $this;

        });
    }

    /**
     * Returns the project that this status category belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Returns the issue status category that this status type belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(IssueStatusCategory::class, 'issue_status_category_id');
    }
}