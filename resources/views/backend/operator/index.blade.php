@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Operator</h1>
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
                <h3>Operators</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Channel</th>
                            <th>Country</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($operators->count() > 0)
                        @foreach($operators as $operator)
                        <tr>
                            <td> {{ $operator->id }}</td>
                            <td> {{ $operator->title }}</td>
                            <td> {{ $operator->channel }}</td>
                            <td> {{ $operator->country->name }}</td>
                            <td class="row">
                                @if(Auth::user()->admin == true)
                                {!! Form::open(array('class' => 'form-inline col-xs-2','method' => 'DELETE', 'action' => array('AdminOperatorController@destroy', $operator->id))) !!}
                                <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Delete" type="submit" onclick="return confirm('Are you sure you want to delete this ?')">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                                {!! Form::close() !!}
                                {!! Form::open(array('class' => 'form-inline col-xs-2','method' => 'GET', 'action' => array('AdminOperatorController@edit', $operator->id))) !!}
                                <button class="btn btn-info btn-sm" type="submit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
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



    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#sub-item-4').addClass('collapse in');
    $('#sub-item-4').parent().addClass('active').siblings().removeClass('active');
</script>
