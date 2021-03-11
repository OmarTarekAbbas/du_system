<div class="box-body table-responsive no-padding">
    <table class="table table-hover table-striped mt-table">
        <thead>
            <tr>
                <th>Total Today Charges</th>
                <th>Total Today Charges Status Success</th>
                <th>Total Today Charges Status Fail</th>
                <th>Total Subscribers</th>
                <th>Weekly Reminder</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td> {{ $total_today_charges }}</td>
                <td> {{ $charges_status_success_today }}</td>
                <td> {{ $charges_status_fail_today }}</td>
                <td> {{ $get_all_subscribers }} </td>
                <td> {{ $log_messages_table_today }} </td>
        </tbody>
    </table>
</div>
