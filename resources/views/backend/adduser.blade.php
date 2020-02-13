@include('backend.header')
<style>
.panel {
  width: 95%;
  margin: 100px auto 0px;
  padding-top: 0px;
}
</style>
 <div class="col-lg-12">
        <h1 class="page-header">Add User</h1>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="canvas-wrapper col-lg-4">

                    {!! Form::open(['url'=>'admin/user', 'class'=>'mtform']) !!}
                        <div class="form-group row">
                            {!! Form::label('name', 'Full Name', ['class'=>'col-sm-12 control-label']) !!}
                            {!! Form::text('name', null, ['class'=>'form-control flat']) !!}
                        </div>
                        <div class="form-group row">
                            {!! Form::label('email', 'E-Mail', ['class'=>'col-sm-12 control-label']) !!}
                            {!! Form::email('email', null, ['class'=>'form-control flat']) !!}
                        </div>
                        <div class="form-group row">
                            {!! Form::label('password', 'Password', ['class'=>'col-sm-12 control-label']) !!}
                            {!! Form::password('password', ['class'=>'form-control flat']) !!}
                        </div>
                        <div class="form-group row">
                            {!! Form::label('admin', 'Admin', ['class'=>'col-sm-12 control-label']) !!}
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