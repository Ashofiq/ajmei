<style>
    .table td, th{
        border: 1px solid black;
        padding:3px;
    }
    table tr, th{
        border: 1px solid black
    }
</style>


<div class="row">
    <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
    <!-- <div class="col-sm-6">
        <dl id="dt-list-1" class="dl-horizontal">
        <dt>Receive Date:</dt><dd>&nbsp;{{ $rows_m->raw_order_date }}</dd>
        <dt>Supplier Name:</dt><dd>&nbsp;{{ $rows_m->supp_name }}</dd>
        <dt>Supplier Phone: </dt><dd>&nbsp;{{ $rows_m->supp_mobile }}</dd>
        <dt>Address:</dt><dd>&nbsp;{{ $rows_m->supp_add1 }} {{ $rows_m->supp_add2 }}</dd>
        </dl>
    </div>
    <div class="col-sm-6">
        <dl id="dt-list-1" class="dl-horizontal">
        <dt>PO No:</dt><dd>&nbsp;{{ $rows_m->raw_order_no }}</dd>
        <dt>PI No:</dt><dd>&nbsp;{{ $rows_m->raw_pi_no }}</dd>
        <dt>Total Qty:</dt><dd>&nbsp;{{ $rows_m->raw_total_qty }}</dd>
        <dt>Total Amount:</dt><dd>&nbsp;{{ $rows_m->raw_total_amount }}</dd> 
        </dl>
    </div> -->

    <div class="row">
        <h4 style="text-align: center;">MRR REPORT <br> Metarial Receiving Report</h4>
    </div>

    <hr>

    <table>
        <tr>
            <td width="20%"><b>Receive Date</b></td>
            <td width="20%">: {{ $rows_m->raw_order_date }}</td>
            <td width="20%"><b>PO No</b></td>
            <td width="20%">: {{ $rows_m->raw_order_no }}</td>
        </tr>
        <tr>
            <td width="20%"><b>Supplier Name</b></td>
            <td width="20%">: {{  $rows_m->supp_name }}</td>
            <td width="20%"><b>PI No</b></td>
            <td width="20%">: {{ $rows_m->raw_pi_no}}</td>
        </tr>
        <tr>
            <td width="20%"><b>Supplier Phone</b></td>
            <td width="20%">: {{  $rows_m->supp_mobile }}</td>
            <td width="20%"><b>Total Qty</b></td>
            <td width="20%">: {{ $rows_m->raw_total_qty }}</td>
        </tr>
        <tr>
            <td width="20%"><b>Address</b></td>
            <td width="20%">: {{  $rows_m->supp_add1 }} {{ $rows_m->supp_add2 }}</td>
            <td width="20%"><b>Total Amount</b></td>
            <td width="20%">: {{ $rows_m->raw_total_amount }}</td>
        </tr>
    </table>
    <br>
    <div class="row">
        <div class="col-sm-12">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Item&nbsp;Code</th>
                <th>Item&nbsp;Name</th>
                <th>Item&nbsp;Specification</th>
                <th>Remarks</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>  
            </tr>
            </thead>
            <tbody>
                @foreach($rows_d as $p_details)
                <?php
                $raw_amount = $p_details->raw_item_qty*$p_details->raw_item_price;
                $raw_amount_bdt = $p_details->raw_item_qty*$p_details->raw_item_price*$p_details->raw_d_curr_rate;
                ?>
                <tr>
                <td>{{ $p_details->item_code }}</td>
                <td>{{ $p_details->item_name }}({{ $p_details->itm_cat_name }})</td>
                <td>{{ $p_details->raw_item_desc }}</td>
                <td>{{ $p_details->raw_item_remarks }}</td>
                <td align="right">{{ number_format($p_details->raw_item_price, 2) }}</td>
                <td align="right">{{ $p_details->raw_item_qty }}</td>
                <td align="right">{{ number_format($raw_amount, 2) }}</td> 
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

</div>

