@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Log Messages</h1>
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

    <br>
    <div class="form-group">
        {!! Form::open(['url' => url('admin/logmessage'),'method'=>'get']) !!}

        <div class="col-md-2">
            {!! Form::label('service_id', 'Select Service :') !!}
            <select name="service_id" class="form-control" id="service_id">
                <option value="">Select Service</option>
                @foreach(\App\Service::all() as $service)
                    <option {{request()->get('service_id') == $service->title ? 'selected' : ''}} value="{{$service->title}}">{{ $service->title." | " . $service->operator->title . ' - '.$service->operator->country->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            {!! Form::label('ms', 'Msisdn:') !!}
            <div class='input-group date'>
                <input type='text' id="ms" class="form-control" value="{{request()->get('msisdn')}}" name="msisdn" />
                <span class="input-group-btn">
                    <button  type="button" id="search-btn" class="btn"><i class="glyphicon glyphicon-search"></i></button>
                </span>
            </div>
        </div>

        <div class="col-md-2">
            {!! Form::label('msg', 'Message:') !!}
            <div class='input-group date'>
                <input type='text' id="ms" class="form-control" value="{{request()->get('message')}}" name="message" />
                <span class="input-group-btn">
                    <button  type="button" id="search-btn" class="btn"><i class="glyphicon glyphicon-search"></i></button>
                </span>
            </div>
        </div>

        <div class="col-md-2">
            {!! Form::label('message_type', 'Select type :') !!}
            <select name="message_type" class="form-control" id="message_type">
                <option value="">Select type</option>
                    <option {{request()->get('message_type') == 'Today_Messages_Schedule'? 'selected' : ''}} value="Today_Messages_Schedule">Today Messages Schedule</option>
                    <option {{request()->get('message_type') == 'pincode'? 'selected' : ''}} value="pincode">pincode</option>
            </select>
        </div>

        <div class="col-md-2">
            {!! Form::label('date', 'Select Date :') !!}
            <div class='input-group date' id='datetimepicker'>
                <input type='text' class="form-control" value="{{request()->get('created')}}" name="created" id="date" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>

        <div class="col-md-1">
            <br>
            <button class="btn btn-labeled btn-info filter" type="submit"><span class="btn-label"><i class="glyphicon glyphicon-search"></i></span>Filter</button>
        </div>

        <div class="col-md-1">
            {!! Form::label('date', 'Count :') !!}
            <div class='input-group date'>
                <span dir="rtl" class="btn btn-success">{{ count($messages) }} </span>
            </div>
        </div>
        {!! Form::close() !!}

    </div>

    <div class="col-xs-12">
        <div class="box">
            <div class="box-title">
                @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
                @endif
                <h3>messages</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>service</th>
                            <th>msisdn</th>
                            <th>message</th>
<<<<<<< HEAD
                            <th>message_type</th>
                            <th>status</th>
=======
                            <th>link</th>
                            <th>Date </th>
>>>>>>> 82a778a42f607e7f124e52b966fac6895636631e
                        </tr>
                    </thead>
                    <tbody>
                        @if($messages->count() > 0)
                        @foreach($messages as $item)
                        <tr>
                            <td> {{ $item->id }}</td>
                            <td> {{ $item->service }} </td>
                            <td> {{ $item->msisdn }}</td>
                            <td> {{ $item->message }}</td>
                            <td> {{ $item->message_type }} </td>
                            <td> {{ $item->status? 'Success' : 'Fail' }} </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>

        @if(!$without_paginate)
        {!! $messages->setPath('logmessage') !!}
        @endif

    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#logmessage').addClass('active').siblings().removeClass('active');
    $('#datetimepicker').datepicker({
        format: "yyyy-mm-dd"
    });
    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });
</script>
