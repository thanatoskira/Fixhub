<?php

/*
 * This file is part of Fixhub.
 *
 * Copyright (C) 2016 Fixhub.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fixhub\Http\Controllers\Admin;

use Fixhub\Bus\Jobs\SetupProject;
use Fixhub\Http\Controllers\Controller;
use Fixhub\Http\Requests\StoreProjectRequest;
use Fixhub\Models\ProjectGroup;
use Fixhub\Models\Project;
use Fixhub\Models\DeployTemplate;
use Illuminate\Http\Request;

/**
 * The controller for managging projects.
 */
class ProjectController extends Controller
{
    /**
     * Shows all projects.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $projects = Project::orderBy('name')
                    ->get();

        $groups = ProjectGroup::orderBy('order')
                    ->get();

        $templates = DeployTemplate::orderBy('name')
                    ->get();

        return view('admin.projects.index', [
            'is_secure' => $request->secure(),
            'title'     => trans('projects.manage'),
            'templates' => $templates,
            'groups'    => $groups,
            'projects'  => $projects->toJson(), // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  StoreProjectRequest $request
     * @return Response
     */
    public function store(StoreProjectRequest $request)
    {
        $fields = $request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url',
            'template_id',
            'allow_other_branch',
            'include_dev',
            'private_key'
        );

        $template_id = null;
        if (array_key_exists('template_id', $fields)) {
            $template_id = array_pull($fields, 'template_id');
        }

        if (array_key_exists('private_key', $fields) && empty($fields['private_key'])) {
            unset($fields['private_key']);
        }

        $project = Project::create($fields);

        $template = DeployTemplate::find($template_id);

        if ($template) {
            dispatch(new SetupProject(
                $project,
                $template
            ));
        }

        return $project;
    }

    /**
     * Update the specified project in storage.
     *
     * @param  int                 $project_id
     * @param  StoreProjectRequest $request
     * @return Response
     */
    public function update($project_id, StoreProjectRequest $request)
    {
        $project = Project::findOrFail($project_id);

        $project->update($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url',
            'allow_other_branch',
            'include_dev',
            'private_key'
        ));

        return $project;
    }

    /**
     * Remove the specified model from storage.
     *
     * @param  int      $project_id
     * @return Response
     */
    public function destroy($project_id)
    {
        $project = Project::findOrFail($project_id);

        $project->delete();

        return [
            'success' => true,
        ];
    }
}
