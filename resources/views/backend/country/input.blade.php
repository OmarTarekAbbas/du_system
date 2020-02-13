
<div class="form-group row">
     <label class="control-label">Title <span class="text-danger">*</span></label>
    {!! Form::text('name', null, ['class'=>'form-control flat']) !!}
</div>

<div class="form-group">
     {!! Form::submit($buttonAction,['class'=>'btn btn-primary']) !!}
</div>
