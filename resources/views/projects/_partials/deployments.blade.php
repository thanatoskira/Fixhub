<div class="box">
    <div class="box-header">
        <h3 class="box-title">{{ trans('deployments.latest') }}</h3>
    </div>

    @if (!count($deployments))
    <div class="box-body">
        <p>{{ trans('deployments.none') }}</p>
    </div>
    @else
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans('app.date') }}</th>
                    <th>{{ trans('deployments.started_by') }}</th>
                    <th>{{ trans('deployments.deployer') }}</th>
                    <th>{{ trans('deployments.committer') }}</th>
                    <th>{{ trans('deployments.commit') }}</th>
                    <th>{{ trans('deployments.branch') }}</th>
                    <th>{{ trans('app.status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deployments as $deployment)
                <tr id="deployment_{{ $deployment->id }}">
                    <td>{{ $deployment->started_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        {{ $deployment->is_webhook ? trans('deployments.webhook') : trans('deployments.manually') }}
                        @if (!empty($deployment->reason))
                            <i class="ion ion-ios-chatboxes-outline deploy-reason" data-toggle="tooltip" data-placement="right" title="{{ $deployment->reason }}"></i>
                        @endif
                    </td>
                    <td>
                        @if ($deployment->build_url)
                            <a href="{{ $deployment->build_url }}" target="_blank">{{ $deployment->deployer_name }}</a>
                        @else
                            {{ $deployment->deployer_name }}
                        @endif
                    </td>
                    <td>{{ $deployment->committer_name }}</td>
                    <td>
                        @if ($deployment->commit_url)
                        <a href="{{ $deployment->commit_url }}" target="_blank">{{ $deployment->short_commit_hash }}</a>
                        @else
                        {{ $deployment->short_commit_hash }}
                        @endif
                    </td>
                    <td><a href="{{ $deployment->branch_url }}" target="_blank"><span class="label label-default">{{ $deployment->branch }}</span></a></td>
                    <td>
                        <span class="label label-{{ $deployment->css_class }}"><i class="ion ion-{{ $deployment->icon }}"></i> <span>{{ $deployment->readable_status }}</span></span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            @if ($deployment->isSuccessful())
                                <button type="button" data-toggle="modal" data-backdrop="static" data-target="#redeploy" data-optional-commands="{{ $deployment->optional_commands_used }}" data-deployment-id="{{ $deployment->id }}" class="btn btn-default btn-rollback @if ($deployment->isCurrent()) hide @endif" title="{{ trans('deployments.rollback') }}"><i class="ion ion-arrow-return-left"></i></button>
                            @endif

                            @if ($deployment->isPending() || $deployment->isRunning())
                                <!-- FIXME: Try and change this to a form as abort should be a POST request -->
                                <a href="{{ route('deployments.abort', ['id' => $deployment->id]) }}" class="btn btn-default btn-cancel" title="{{ trans('deployments.cancel') }}"><i class="ion ion-eye-disabled"></i></a>
                            @endif
                            <a href="{{ route('deployments', ['id' => $deployment->id]) }}" type="button" class="btn btn-default" title="{{ trans('app.details') }}"><i class="ion ion-information-circled"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {!! $deployments->render() !!}
    </div>

    @endif
</div>
