@extends('layouts.app_m')
@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="col-sm-6">
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
  </div>

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

@stop

@section('pagescript')

<script type="text/javascript">
  $(document).ready(function() {

  });
</script>

@stop
