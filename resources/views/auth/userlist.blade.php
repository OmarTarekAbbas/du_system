@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Users</h1>
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

            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Admin</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($Users as $User)
                        <tr>
                            <td>{{ $User->name }}</td>

                            <td>
                                @if($User->admin == 0)
                                    No
                                @else
                                    Yes
                                @endif
                            </td>
                            <td>{{ $User->email }}</td>
                            <td>
                                {!! Form::open(array('class' => 'form-inline col-xs-2','method' => 'DELETE', 'action' => array('UsersController@destroy', $User->id))) !!}
                                <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Delete" type="submit" onclick="return confirm('Are you sure you want to delete this ?')">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                                {!! Form::close() !!}
                                {!! Form::open(array('class' => 'form-inline col-xs-2','method' => 'GET', 'action' => array('UsersController@edit', $User->id))) !!}
                                <button class="btn btn-info btn-sm" type="submit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </button>
                                {!! Form::close() !!}

                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
        </div>



    </div>


</div>

@include('backend.footer')
