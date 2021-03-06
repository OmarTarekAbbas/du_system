@include('backend.header')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Services Statistics</h1>
    </div>
</div><!--/.row-->

<div class="row">


    <div class="col-xs-12">
        <div class="box">
            <div class="box-title">
                <h3>Services</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped mt-table">
                    <thead>
                    <tr>
                        <th>Service</th>
                        <th> {{ $service->title }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>All Activation Number</th>
                            <th> {{ $activations}}</th>
                        </tr>
                        <tr>
                            <th>Activation Number Without Dublicates</th>
                            <th> {{ $activationsWithoutDublicates}}</th>
                        </tr>
                        <tr>
                            <th>Faild Activation Number</th>
                            <th> {{ $faildActivation}}</th>
                        </tr>
                        <tr>
                            <th>Subscriber Number</th>
                            <th> {{ $subscribers}}</th>
                        </tr>
                        <tr>
                            <th>Today's Messages Number</th>
                            <th> {{ $msgs}}</th>
                        </tr>
                        <tr>
                            <th>Today's Success Messages Number</th>
                            <th> {{ $successmsgs}}</th>
                        </tr>
                        <tr>
                            <th>Today's Failed Messages Number</th>
                            <th> {{ $failedmsgs}}</th>
                        </tr>
                        <tr>
                            <th>UnSubscribers Number</th>
                            <th> {{ $unsubscribers}}</th>
                        </tr>

                        <tr>
                            <th>Today Charges</th>
                            <th> {{ $charge_date}}</th>
                        </tr>
                        <tr>
                            <th>Status Code 0</th>
                            <th> {{ $charge_status_0}}</th>
                        </tr>
                        <tr>
                            <th>Status Code 503 - product already purchased!</th>
                            <th> {{ $charge_status_503}}</th>
                        </tr>
                        <tr>
                            <th>Status Code 24 - Insufficient funds.</th>
                            <th> {{ $charge_status_24}}</th>
                        </tr>
                        <tr>
                            <th>Status Code Failed</th>
                            <th> {{ $failed}}</th>
                        </tr>


                        <tr>
                            <th>All Status calculate</th>
                            <th> {{ $charge_status_0 +  $charge_status_503 +$charge_status_24 + $failed}}

                                   /
                                {{ $charge_status_0 +  $charge_status_503 +$charge_status_24 + $failed == $charge_date ? "Correct" : "Not Correct"}}
                            </th>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>


    </div>
</div>

@include('backend.footer')
<script type="text/javascript">
    $('#sub-item-2').addClass('collapse in');
    $('#sub-item-2').parent().addClass('active').siblings().removeClass('active');
</script>
