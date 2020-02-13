@include('backend/header')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit Messages</h1>
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
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="canvas-wrapper col-lg-4">
                    {!! Form::model($Mt,['method'=>'PATCH','action'=>['MtController@update',$Mt->id],'files'=>true]) !!}
                    
                    <div class="form-group">
                        {!! Form::label('MTBody', null, ['class'=>'col-sm-2 control-label']) !!}
                        {!! Form::textarea('MTBody', null, ['rows'=>10,'cols'=>50,'class'=>'form-control flat','required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('date', null, ['class'=>'col-sm-2 control-label']) !!}
                        
                        <div class='input-group date' id='datetimepicker1'>
                        {!! Form::text('date', null, ['class'=>'form-control','required']) !!}
                            
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                        </div>
                    </div>
                    
                       <div class="form-group">
                        {!! Form::label('time', null, ['class'=>'col-sm-2 control-label']) !!}

                        <div class='input-group'>
                            {!! Form::select('time', [''=>'Select Time']+  Helper::time() , null, ['class'=>'form-control']) !!}


                            
                        </div>
                    </div>

                    <div class="form-group">
                        <p>&nbsp;&nbsp;
                            <label>Media Resource</label>&nbsp;
                            <input type="radio" checked="" value="option1" id="optionsRadios1" name="optionsRadios">External Link
                            &nbsp;<input type="radio" value="option2" id="optionsRadios2" name="optionsRadios">File
                        </p>
                    </div>

                    <div class="container"></div>

                    <div class="form-group urldiv">
                        {!! Form::label('MTURL', 'URL :', ['class'=>'col-sm-2 control-label']) !!}
                        {!! Form::text('MTURL', null, ['class'=>'form-control']) !!}
                        
                    </div>

                    <!--div class="form-group filediv">
                        <label for="file" class="col-sm-2 control-label">File</label>
                        <input type="file" name="file" id="file" >
                    </div-->

                    <!--div class="form-group">
                        {!! Form::label('services', 'Select Service') !!}
                        {!! Form::select('services[]', $Services, null, ['class'=>'form-control','multiple','id'=>'services_se']) !!}

                    </div-->

                    <div class="alert alert-danger" id="errormsg" style="display:none">
                    </div>

                    <!--div class="form-group">
                        <button type="button" name="checkdata" id="checkdata" class="btn btn-primary">
                            Check data
                        </button>
                    </div-->

                    <div class="form-group">
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div><!--/.row-->

@include('backend/footer')

<script type="text/javascript">
    var url_div =   '<div class="form-group urldiv">'+
            '<label for="url" class="col-sm-2 control-label">URL</label>'+
            '<input class="form-control" placeholder="" name="MTURL" type="text" required>'+
            '</div>';

    var file_div = '<div class="form-group filediv">'+
            '<label for="file" class="col-sm-2 control-label">File</label>'+
            '<input type="file" name="file" id="file" required>'+
            '</div>';

    $(".filediv").hide();
    $("#urldiv").prop('checked', true);
    $("#filediv").prop('checked', false);
    //$("#submit").prop('disabled', true);

    $("#optionsRadios1").click(function(){
        // $(".urldiv").show();
        // $(".filediv").hide();
        $(".container").html(url_div);

    });

    $("#optionsRadios2").click(function(){
        //$(".filediv").show();
        $(".urldiv").remove();
        $(".container").html(file_div);
    });

    $('#datetimepicker1').datepicker({
        format: "yyyy-mm-dd"
    });

    $("#services_se").on('click', function(){
        var value = $(this).val();
        var date = $("#date").val();
        var datastring = 'date='+ date + '&ids=' + value;
        if(date == ""){
            alert ("You shpuld enter date");
        }
        else{
            $.ajax({
                type: "GET",
                url : '{{url('check')}}',
                data : datastring,
                cache: false,
                success: function(response) {
                    // alert(response);
                    if(response != ""){
                        $("#errormsg").css( "display", "block");
                        $("#errormsg").html(response);
                    }
                    else{
                        ("#errormsg").css( "display", "none");
                    }

                }
            });
        }
    });

    /*$('#checkdata').click('submit', function(event){
     var date = $("#date").val();
     var services_se=$("#services_se").val();

     var datastring = 'date='+ date + '&ids=' + services_se;
     $.ajax({
     type: "GET",
     url : '{{url('check')}}',
     data : datastring,
     cache: false,
     datatype: "json",
     success: function(response) {
     alert(response);
     if(response == ""){
     alert("aaaaaaaaaaaaa");
     }
     else{
     alert("bbbbbbbbbbbb");
     }
     }
     });
     });*/
</script>