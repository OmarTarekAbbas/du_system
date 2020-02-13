@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Search Results</h1>
         <div class="col-md-1">
            Count: <b style="padding: 5px;">  {{ $Messages->total() }}</b>
        </div>
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

                <h3>Messages</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Message Body</th>
                        <th>URL</th>
                        <th>Release Date</th>
                          <th>Time</th>
                        <th>Service</th>
                        <th>Operator</th>
                        <th>Sent</th>
                        <th>Shortened URL</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($Messages)
                    @foreach($Messages as $Mt)
                        <tr>
                            <td>{{ $Mt->MTBody }}</td>
                            <td>{{ $Mt->MTURL }}</td>
                            <td> {{ $Mt->date }} </td>
                             <td> {{ Helper::time()[$Mt->time]     }} </td>
                            <td>{{ $Mt->service->title }}</td>
                            <td>{{ $Mt->service->operator->title }} - {{ $Mt->service->operator->country->name }}</td>
                            <td>
                                @if(is_null($Mt->IsysURL))
                                    No
                                @else
                                    Yes
                                @endif
                            </td>
                            <td><a href="{{ $Mt->ShortnedURL }}" target="_blank">{{ $Mt->ShortnedURL }}</a> </td>
                            <td>
                                @if(is_null($Mt->IsysURL))
                                {!! Form::open(array('class' => 'form-inline col-lg-1','method' => 'DELETE', 'action' => array('MtController@destroy', $Mt->id))) !!}
                                <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Delete" type="submit" onclick="return confirm('Are you sure you want to delete this ?')">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                                {!! Form::close() !!}
                                {!! Form::open(array('class' => 'form-inline col-lg-1','method' => 'GET', 'action' => array('MtController@edit', $Mt->id))) !!}
                                <button class="btn btn-info btn-sm" type="submit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </button>
                                {!! Form::close() !!}
                                @endif
                                @if(Auth::user()->admin == true && $Mt->status == false)
                                    {!! Form::open(array('class' => 'form-inline col-lg-1','method' => 'GET', 'action' => array('MtController@approve', $Mt->id))) !!}
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
{!! $Messages->setPath('search')->appends(Input::query()); !!}

    </div>


</div>

@include('backend.footer')
<script type="text/javascript">
    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });
</script>