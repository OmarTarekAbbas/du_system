@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Failds Today Message</h1>
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
        {!! Form::open(['url' => route('admin.faild.charge.get'),'method'=>'get']) !!}

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

        <div class="col-md-1">
            <br>
            <button class="btn btn-labeled btn-info filter" type="submit"><span class="btn-label"><i class="glyphicon glyphicon-search"></i></span>Filter</button>
        </div>

        <div class="col-md-1">
            {!! Form::label('date', 'Count :') !!}
            <div class='input-group date'>
                <span dir="rtl" class="btn btn-success">{{ count($failds) }} </span>
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
                <h3>failds</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>msisdn</th>
                            <th>service</th>
                            <th>plan</th>
                            <th>statusCode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($failds))
                        @foreach($failds as $item)
                        <tr>
                            <td> {{ $item->id }}</td>
                            <td> {{ $item->msisdn }}</td>
                            <td> {{ $item->serviceid }} </td>
                            <td> {{ $item->plan }} </td>
                            <td> {{ $item->status_code }} </td>

                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#faild').addClass('active').siblings().removeClass('active');
    $('#datetimepicker').datepicker({
        format: "yyyy-mm-dd"
    });
    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });
</script>
