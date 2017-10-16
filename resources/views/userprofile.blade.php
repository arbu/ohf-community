@extends('layouts.app')

@section('title', 'User Profile')

@section('content')

    <h1 class="display-4">User Profile</h1>
    <br>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <i class="fa fa-warning"></i> Validation failed, you have entered invalid values!
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fa fa-check"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Profile</div>
                <div class="card-body">
                    {!! Form::open(['route' => ['userprofile.update']]) !!}
                        <div class="form-group">
                            {{ Form::label('name', 'Name') }}
                            {{ Form::text('name', $user->name, [ 'class' => 'form-control'.($errors->has('name') ? ' is-invalid' : ''), 'required', 'autofocus' ]) }}
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', 'E-Mail Address') }}
                            {{ Form::text('email', $user->email, [ 'class' => 'form-control'.($errors->has('email') ? ' is-invalid' : ''), 'required' ]) }}
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        {{ Form::button('<i class="fa fa-save"></i> Update', [ 'type' => 'submit', 'class' => 'btn btn-primary' ]) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Account information</div>
                <div class="card-body">
                    <p>Your account has been created on <strong>{{ $user->created_at }}</strong> 
                        and last updated on <strong>{{ $user->updated_at }}</strong>.</p>
                    {!! Form::open(['route' => ['userprofile.delete'], 'method' => 'delete']) !!}
                        {{ Form::button('<i class="fa fa-user-times"></i> Delete account', [ 'type' => 'submit', 'class' => 'btn btn-danger', 'id' => 'delete_account_button' ]) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    $( '#delete_account_button' ).on('click', function(){
        return confirm('Do you really want to delete your account and lose access to all data?');
    });
@endsection
