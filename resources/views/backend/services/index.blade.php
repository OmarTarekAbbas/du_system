@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">All Services</h1>
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
                <h3>Services</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>service</th>
                            <th>language</th>
                            <th>Type</th>
                            <th>Operator</th>
                            <th>URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($services->count() > 0)
                        @foreach($services as $service)
                        <tr>
                            <td> {{ $service->id }}</td>
                            <td> {{ $service->title }}</td>
                            <td> {{ $service->service }}</td>
                            <td> {{ $service->lang }} </td>
                            <td> {{ $service->type }} </td>
                            <td> {{ $service->operator->title .' - '. $service->operator->country->name }}</td>
                            <td> {{ $service->ExURL }} </td>                                                    
                            <td class="row">
                                @if(Auth::user()->admin == true)
                                <a class="btn btn-sm btn-default" title="Edit" href='{{url("admin/services/$service->id/edit")}}'><span class="glyphicon glyphicon-pencil"></span></a> 
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>

        {!! $services->setPath('services') !!}

    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#sub-item-2').addClass('collapse in');
    $('#sub-item-2').parent().addClass('active').siblings().removeClass('active');
</script>