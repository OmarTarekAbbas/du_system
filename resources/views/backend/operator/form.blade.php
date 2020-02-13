@include('backend.header')
<style>
    .panel {
        width: 95%;
        margin: 100px auto 0px;
        padding-top: 0px;
    }
</style>
<div class="col-lg-12">
    <h1 class="page-header"> <?= $operator ? 'Edit' : 'Add' ?> Operator</h1>
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

                @if($operator)
                {!! Form::model($operator,["url"=>"admin/operator/$operator->id","method"=>"patch"]) !!}
                @include('backend.operator.input',['buttonAction'=>'Edit'])
                @else
                {!! Form::open(["url"=>"admin/operator/","method"=>"POST"]) !!}
                @include('backend.operator.input',['buttonAction'=>'Save'])
                @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@include('backend.footer')
<script type="text/javascript">
    $('#sub-item-4').addClass('collapse in');
    $('#sub-item-4').parent().addClass('active').siblings().removeClass('active');
</script>
