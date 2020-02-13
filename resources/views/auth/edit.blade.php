@include('backend.header')
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="canvas-wrapper col-lg-4">
                {!! Form::model($User,['method'=>'PATCH','action'=>['UsersController@update',$User->id],]) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Full Name', ['class'=>'col-sm-2 control-label']) !!}
                    {!! Form::text('name', null, ['class'=>'form-control flat']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'E-Mail', ['class'=>'col-sm-2 control-label']) !!}
                    {!! Form::email('email', null, ['class'=>'form-control flat']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password', 'Password', ['class'=>'col-sm-2 control-label']) !!}
                    <input type="password" name="password" id="password" value="{{$User->password}}" class="form-control flat">

                </div>
                <div class="form-group">
                    {!! Form::label('admin', 'Admin', ['class'=>'col-sm-2 control-label']) !!}
                    {!! Form::select('admin', [0=>'User',1=>'Admin'], null, ['class'=>'form-control flat']) !!}
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" id="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@include('backend.footer')