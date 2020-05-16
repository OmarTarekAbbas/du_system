@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Charges</h1>
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
        {!! Form::open(['url' => route('admin.charges.index'),'method'=>'get']) !!}

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
                {!! Form::select('status', ["0" => "0" ,'503 - product already purchased!'=>'503 - product already purchased' , '24 - Insufficient funds.' => '24 - Insufficient funds','fail' => 'Failed'] , request()->get('status'), ['class'=>'form-control','id'=>'plan','placeholder'=>'Select Status']) !!}
            </div>
        </div>

        <div class="col-md-2">
            {!! Form::label('date', 'Select From  Date :') !!}
            <div class='input-group date' id='datetimepicker'>
                <input type='text' class="form-control" value="{{request()->get('from_date')}}" name="from_date" id="date" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>



        <div class="col-md-2">
            {!! Form::label('date1', 'Select To  Date :') !!}
            <div class='input-group date' id='datetimepicker1'>
                <input type='text' class="form-control" value="{{request()->get('to_date')}}" name="to_date" id="date1" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>

        @if($without_paginate)
        <?php
        $sum = 0;
        foreach($charges as $charge){
            if(!$charge->charge_status_code){
                $sum+=$charge->price;
            }
        }
        ?>
        <div class="col-md-1">
            {!! Form::label('date', 'Total Price :') !!}
            <div class='input-group date'>
                <span dir="rtl" class="btn btn-success">{{ $sum }} دينار</span>
            </div>
        </div>
        @endif

        <div class="col-md-1">
            <br>
            <button class="btn btn-labeled btn-info filter" type="submit"><span class="btn-label"><i class="glyphicon glyphicon-search"></i></span>Filter</button>
        </div>

        <div class="col-md-1">
            {!! Form::label('date', 'Count :') !!}
            <div class='input-group date'>
                <span dir="rtl" class="btn btn-success">{{ count($charges) }} </span>
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
                <h3>Charges</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>msisdn</th>
                            <th>service</th>
                            <th>plan</th>
                            <th>price</th>
                            <th>charging_date</th>
                            <th>statusCode</th>
                            <th>subscribe date</th>
                            <th>next charging date</th>
                            <th>final status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($charges->count() > 0)
                        @foreach($charges as $item)
                        <tr>
                            <td> {{ $item->charge_id }}</td>
                            <td> {{ $item->msisdn }}</td>
                            <td> {{ $item->serviceid }} </td>
                            <td> {{ $item->plan }} </td>
                            <td> {{ $item->price }} </td>
                            <td> {{ $item->charging_date }} </td>
                            <td> {{ $item->charge_status_code }} </td>
                            <td> {{ $item->subscribe_date }}</td>
                            <td> {{ $item->next_charging_date}}</td>
                            <td> {{ $item->final_status}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
        @if(!$without_paginate)
            {!! $charges->setPath('charges') !!}
        @endif
    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#charge').addClass('active').siblings().removeClass('active');
    $('#datetimepicker').datepicker({
        format: "yyyy-mm-dd"
    });
    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });
</script>
