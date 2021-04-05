@include('backend.header')
<style>
.panel {
  width: 95%;
  margin: 100px auto 0px;
  padding-top: 0px;
}
</style>
    <div class="col-lg-12">
        <h1 class="page-header">Profile</h1>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="canvas-wrapper col-lg-4">
                @if(Session::has('success'))
                    <p class="alert alert-success" style="padding-bottom: 11px">{{ Session::get('success') }} <a href="#"  style="margin-bottom: 5px;"  class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
                    {!! Form::open(['url'=>'admin/profile', 'class'=>'mtform']) !!}
                        <div class="form-group row">
                            {!! Form::label('name', 'Full Name', ['class'=>'col-sm-12 control-label']) !!}
                            {!! Form::text('name', auth()->user()->name, ['class'=>'form-control flat', "disabled" => "disabled"]) !!}
                        </div>
                        <div class="form-group row">
                            {!! Form::label('email', 'E-Mail', ['class'=>'col-sm-12 control-label']) !!}
                            {!! Form::email('email', auth()->user()->email, ['class'=>'form-control flat', "disabled" => "disabled"]) !!}
                        </div>
                        <div class="form-group row">
                            {!! Form::label('password', 'Password', ['class'=>'col-sm-12 control-label']) !!}
                            {!! Form::password('password', ['class'=>'form-control flat']) !!}
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
