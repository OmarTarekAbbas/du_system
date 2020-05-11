@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Activations</h1>
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
        {!! Form::open(['url' => route('admin.activations.index'),'method'=>'get']) !!}

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
            {!! Form::label('se', 'Service:') !!}
            <div class=''>
                {!! Form::select('serviceid', $services , request()->get('serviceid'), ['class'=>'form-control','id'=>'se','placeholder'=>'Select Services']) !!}
            </div>
        </div>

        <div class="col-md-2">
            {!! Form::label('plan', 'Plan:') !!}
            <div class=''>
                {!! Form::select('plan', ['daily'=>'daily' , 'weekly' => 'weekly'] , request()->get('plan'), ['class'=>'form-control','id'=>'plan','placeholder'=>'Select Plan']) !!}
            </div>
        </div>

        <div class="col-md-2">
            {!! Form::label('Status', 'Status:') !!}
            <div class=''>
                {!! Form::select('status', ['0' => '0' ,'503 - product already purchased!'=>'503 - product already purchased' , '24 - Insufficient funds.' => '24 - Insufficient funds'] , request()->get('status'), ['class'=>'form-control','id'=>'plan','placeholder'=>'Select Status']) !!}
            </div>
        </div>

        <div class="col-md-2">
            {!! Form::label('date', 'Select Activation Date :') !!}
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
                <span dir="rtl" class="btn btn-success">{{ count($activations) }} </span>
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
                <h3>activations</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>msisdn</th>
                            <th>trxid</th>
                            <th>service</th>
                            <th>plan</th>
                            <th>statusCode</th>
                            <th>Activation Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($activations->count() > 0)
                        @foreach($activations as $item)
                        <tr>
                            <td> {{ $item->id }}</td>
                            <td> {{ $item->msisdn }}</td>
                            <td> {{ $item->trxid }}</td>
                            <td> {{ $item->serviceid }} </td>
                            <td> {{ $item->plan }} </td>
                            <td> {{ $item->status_code }} </td>
                            <td> {{ $item->created_at->format('d-m-Y') }} </td>

                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>

        @if(!$without_paginate)
        {!! $activations->setPath('activations') !!}
        @endif

    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#activations').addClass('active').siblings().removeClass('active');
    $('#datetimepicker').datepicker({
        format: "yyyy-mm-dd"
    });
    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });
</script>
