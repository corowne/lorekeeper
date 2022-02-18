@extends('admin.layout')

@section('admin-title') Staff Reward Settings @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Staff Reward Settings' => 'admin/staff-reward-settings']) !!}

@if(!Config::get('lorekeeper.extensions.staff_rewards.enabled'))
    <div class="alert alert-danger">
        Staff rewards are currently disabled. Enable them in the Lorekeeper configuration files to use this feature.
    </div>
@endif

<h1>Staff Reward Settings</h1>

<p>This is a list of staff actions that are configured to receive rewards. Staff members will be granted the value configured in {!! isset($currency) && $currency ? $currency->displayName : '(Invalid Currency - Configure ID in config/lorekeeper/extensions.php)' !!} when they perform the relevant action(s). Set an action's value to 0 to disable rewards for it.</p>

@if(!count($settings))
    <p>No settings found.</p>
@else
    {!! $settings->render() !!}
    <table class="table table-sm setting-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Value</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td>{{ $setting->name }}</td>
                    <td>{{ $setting->description }}</td>
                    <td>
                        {!! Form::open(['url' => 'admin/staff-reward-settings/'.$setting->key, 'class' => 'd-flex justify-content-end']) !!}
                            <div class="form-group mr-3 mb-3">
                                {!! Form::text('value', $setting->value, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group mb-3">
                                {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                            </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    {!! $settings->render() !!}
@endif

@endsection
