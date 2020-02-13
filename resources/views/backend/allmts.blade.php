@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Messages</h1>
    </div>
</div><!--/.row-->

<div class="row">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <div class="form-search">
        {!! Form::open(['url'=>'admin/mt/search','method'=>'get']) !!}
        <div class="input-group">
            {!! Form::text('search', null, ['class'=>'form-control','placeholder'=>'Search ..']) !!}
                        <span class="input-group-btn">
                            <button  type="submit" id="search-btn" class="btn"><i class="glyphicon glyphicon-search"></i></button>
                        </span>
        </div>
        {!! Form::close() !!}
        </div>
        <br>
        <div class="form-group">
            {!! Form::open(['url'=>'admin/mt/filter','method'=>'get']) !!}
            <div class="col-md-2">
                @if(Auth::user()->admin == true)
                <div class="input-group">
                    {!! Form::label('service_id', 'Select Service :') !!}
                    <select name="service_id" class="form-control" id="service_id">
                        <option value="0">Select Service</option>
                        @foreach(\App\Service::all() as $service)
                            <option value="{{$service->id}}">{{ $service->title." | " . $service->operator->title . ' - '.$service->operator->country->name }}</option>
                        @endforeach
                    </select>

                </div>
                @endif
            </div>
            <div class="col-md-2">
                {!! Form::label('date', 'Select Date :') !!}
                <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" name="date" id="date" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                </div>
            </div>

            <div class="col-md-2">
                    <div class="input-group">
                        {!! Form::label('status', 'Select Status :') !!}
                        <select name="status" class="form-control" id="service_id">
                            <option value="2">Select Status</option>
                            <option value="1">Approved</option>
                            <option value="0">Not Approved</option>

                        </select>

                    </div>
            </div>

            <div class="col-md-1">
                <br>
                <button class="btn btn-labeled btn-info filter" type="submit"><span class="btn-label"><i class="glyphicon glyphicon-search"></i></span>Filter</button>
            </div>


            {!! Form::close() !!}
        </div>


    <div class="col-xs-12">
        <div class="box">
            <div class="box-title">

                <h3>Messages</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                    <tr>
                        <th>Message Body</th>
                        <th>URL</th>
                        <th>Release Date</th>
                          <th>Release Time</th>
                        <th>Service</th>
                        <th>Operator</th>
                        <th>Sent</th>
                        <th>Shortened URL</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>




                  @if($Messages->count() > 0)
                    @foreach($Messages as $Mt)
                        <tr>
                            <td>{{ $Mt->MTBody }}</td>
                            <td>{{ $Mt->MTURL }}</td>
                            <td> {{ $Mt->date }} </td>
                            <td> {{ Helper::time()[$Mt->time]     }} </td>
                         <td>{{ isset($Mt->service->title) ?$Mt->service->title  : "" }}</td>

                            <td>{{  isset($Mt->service->operator->title)?$Mt->service->operator->title  : ""  }} - {{ isset($Mt->service->operator->country->name)? $Mt->service->operator->country->name  :  "" }}</td>
                            <td>
                                 @if(  $Mt->IsysResponse == 'OK'	)
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td><a href="{{ $Mt->ShortnedURL }}" target="_blank">{{ $Mt->ShortnedURL }}</a> </td>
                            <td class="row">
                                @if(is_null($Mt->IsysURL))
                                {!! Form::open(array('class' => 'col-xs-4','method' => 'DELETE', 'action' => array('MtController@destroy', $Mt->id))) !!}
                                <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Delete" type="submit" onclick="return confirm('Are you sure you want to delete this ?')">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                                {!! Form::close() !!}
                                {!! Form::open(array('class' => 'col-xs-4','method' => 'GET', 'action' => array('MtController@edit', $Mt->id))) !!}
                                <button class="btn btn-info btn-sm" type="submit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </button>
                                {!! Form::close() !!}
                                @endif
                                @if(Auth::user()->admin == true && $Mt->status == false)
                                    {!! Form::open(array('class' => 'col-xs-4','method' => 'GET', 'action' => array('MtController@approve', $Mt->id))) !!}
                                    <button class="btn btn-success btn-sm" type="submit" data-toggle="tooltip" data-placement="bottom" title="Approve">
                                        <i class="glyphicon glyphicon-check"></i>
                                    </button>
                                    {!! Form::close() !!}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                     @endif

                    </tbody>
                </table>

            </div>
        </div>

        {!! $Messages->setPath('mt'); !!}

    </div>


</div>

@include('backend.footer')
<script type="text/javascript">
    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });
</script>
