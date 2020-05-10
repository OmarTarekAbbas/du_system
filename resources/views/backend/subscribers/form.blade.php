@include('backend.header')
<style>
    .panel {
        width: 95%;
        margin: 100px auto 0px;
        padding-top: 0px;
    }
</style>
<div class="col-lg-12">
    <h1 class="page-header">  Subscribe Excel </h1>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="canvas-wrapper col-lg-12">
                {!! Form::open(["url"=>"admin/subscribe/excel","method"=>"POST" , "enctype" => "multipart/form-data"]) !!}
                @include('backend.subscribers.input',['buttonAction'=>'Save'])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@include('backend.footer')
<script type="text/javascript">
    $('#excel').addClass('active').siblings().removeClass('active');
</script>
