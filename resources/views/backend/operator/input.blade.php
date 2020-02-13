<div class="form-group row">
     <label class="control-label">Title <span class="text-danger">*</span></label>
    {!! Form::text('title', null, ['class'=>'form-control flat']) !!}
</div>

<div class="form-group row">
     <label class="control-label">Channel <span class="text-danger">*</span></label>
    {!! Form::text('channel', null, ['class'=>'form-control flat']) !!}
</div>
@if(isset($_REQUEST['country_id']) && $_REQUEST['country_id'])
<div class="form-group row">
   <label class="control-label">Country <span class="text-danger">*</span></label>
   <select name="country_id" class='form-control flat'>
       <option value="{{$_REQUEST['country_id']}}">{{$_REQUEST['title']}}</option>
   </select>
</div>
@else
<div class="form-group row">
   <label class="control-label">Country <span class="text-danger">*</span></label>
   <select name="country_id" class='form-control flat'>
       @foreach($countrys  as $country)
       <option value="{{$country->id}}" @if($operator && $operator->country_id==$country->id) selected @endif>{{$country->name}}</option>
       @endforeach
   </select>
</div>
@endif


<div class="form-group">
     {!! Form::submit($buttonAction,['class'=>'btn btn-primary']) !!}
</div>
