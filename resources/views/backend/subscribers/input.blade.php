
<div class="form-group row">
    <label class="control-label">Excel File <span class="text-danger">*</span></label>
    {!! Form::file('file', null, ['class'=>'form-control flat']) !!}
</div>

<div class="form-group">
     {!! Form::submit($buttonAction,['class'=>'btn btn-primary']) !!}
</div>

