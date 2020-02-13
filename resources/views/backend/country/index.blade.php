@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Country</h1>
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

    <div class="col-xs-12">
        <div class="box">
            <div class="box-title">
                @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
                @endif
                <h3>Countries</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($countrys->count() > 0)
                        @foreach($countrys as $country)
                        <tr>
                            <td> {{ $country->id }}</td>
                            <td> {{ $country->name }}</td>
                            <td class="row">
                                @if(Auth::user()->admin == true)

                                {!! Form::open(array('class' => 'form-inline col-xs-2','method' => 'DELETE', 'action' => array('AdminCountryController@destroy', $country->id))) !!}
                                <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Delete" type="submit" onclick="return confirm('Are you sure you want to delete this ?')">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                                {!! Form::close() !!}
                                {!! Form::open(array('class' => 'form-inline col-xs-2','method' => 'GET', 'action' => array('AdminCountryController@edit', $country->id))) !!}
                                <button class="btn btn-info btn-sm" type="submit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </button>
                                {!! Form::close() !!}
                                <a class="btn btn-sm btn-success show-tooltip" title="Add Operator" href="{{url("admin/operator/create?country_id=".$country->id."&title=".$country->name)}}" data-original-title="Add Operator"><i class="glyphicon glyphicon-plus"></i></a>
                                @endif
                            </td>
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
    $('#sub-item-3').addClass('collapse in');
    $('#sub-item-3').parent().addClass('active').siblings().removeClass('active');
</script>
