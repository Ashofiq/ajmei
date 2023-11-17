@extends('layouts.app_m')
@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Purchase Date:</dt><dd>&nbsp;{{ $rows_m->pur_order_date }}</dd>
      <dt>Supplier Name:</dt><dd>&nbsp;{{ $rows_m->supp_name }}</dd>
      <dt>Supplier Phone: </dt><dd>&nbsp;{{ $rows_m->supp_mobile }}</dd>
      <dt>Address:</dt><dd>&nbsp;{{ $rows_m->supp_add1 }} {{ $rows_m->supp_add2 }}</dd>
    </dl>
  </div>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>PO No:</dt><dd>&nbsp;{{ $rows_m->pur_order_no }}</dd>
      <dt>PI No:</dt><dd>&nbsp;{{ $rows_m->pur_pi_no }}</dd>
      <dt>Total Qty:</dt><dd>&nbsp;{{ $rows_m->pur_total_qty }}</dd>
      <dt>Total Amount:</dt><dd>&nbsp;{{ $rows_m->pur_total_amount }}</dd>
    </dl>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item&nbsp;Wearhouse</th>
            <th>Item&nbsp;Code</th>
            <th>Item&nbsp;Name</th>
            <th>Remarks</th>
            <th>LOT&nbsp;No</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>

          </tr>
        </thead>
        <tbody>
            @foreach($rows_d as $p_details)
            <?php
              $pur_amount = $p_details->pur_item_qty*$p_details->pur_item_price;
            ?>
            <tr>
              <td>{{ $p_details->ware_name }}</td>
              <td>{{ $p_details->item_code }}</td>
              <td>{{ $p_details->item_name }}({{ $p_details->itm_cat_name }})</td>
              <td>{{ $p_details->pur_item_remarks }}</td>
              <td align="center">{{ $p_details->pur_lot_no }}</td>
              <td align="right">{{ number_format($p_details->pur_item_price, 2) }}</td> 
              <td align="right">{{ $p_details->pur_item_qty }}</td>
              <td align="right">{{ number_format($pur_amount, 2) }}</td>
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
