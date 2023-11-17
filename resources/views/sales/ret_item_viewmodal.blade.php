@extends('layouts.app_m')
@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Return Date:</dt><dd>&nbsp;{{ $rows_m->ret_date }}</dd>
      <dt>Customer Name:</dt><dd>&nbsp;{{ $rows_m->cust_name }}</dd>
      <dt>Customer Phone: </dt><dd>&nbsp;{{ $rows_m->cust_mobile }}</dd>
      <dt>Address:</dt><dd>&nbsp;{{ $rows_m->cust_add1 }} {{ $rows_m->cust_add2 }}</dd>
    </dl>
  </div>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Return No:</dt><dd>&nbsp;{{ $rows_m->ret_no }}</dd>
      <dt>Total Bill:</dt><dd>&nbsp;{{ $rows_m->ret_sub_total + $rows_m->ret_itm_disc_value}}</dd>
      <dt>Total Discount:</dt><dd>&nbsp;{{ $rows_m->ret_itm_disc_value + $rows_m->ret_inv_disc_value}}</dd>
      <dt>Total VAT:</dt><dd>&nbsp;{{ $rows_m->ret_vat_value }}</dd>
      <dt>Net Total:</dt><dd>&nbsp;{{ $rows_m->ret_net_amt }}</dd>
    </dl>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item&nbsp;Code</th>
            <th>Item&nbsp;Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Sub&nbsp;Total</th>
            <th>Disc</th>
            <th>Disc.&nbsp;Value</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php $tot_qty = 0;$tot_disc = 0; $tot_sub_amount = 0;$tot_total_disc = 0;?>
            @foreach($rows_d as $sale_details)
            <?php

            $sub_amount = $sale_details->ret_item_price*$sale_details->ret_qty;
            $total_disc = $sale_details->ret_disc_value;
            $sub_total = $sub_amount - $total_disc;

            $tot_disc       += $sale_details->ret_disc_value;
            $tot_qty        += $sale_details->ret_qty;
            $tot_sub_amount += $sub_amount;
            $tot_total_disc += $total_disc;

            ?>
            <tr>
              <td>{{ $sale_details->item_code }}</td>
              <td>{{ $sale_details->item_name }}({{ $sale_details->itm_cat_name }})</td>
              <td align="center">{{ $sale_details->ret_item_price }}</td>
              <td align="center">{{ $sale_details->ret_qty }}</td>
              <td align="right">{{ number_format($sub_amount, 2) }}</td>
              <td align="center">{{ number_format($sale_details->ret_disc_per, 2) }}</td>
              <td align="right">{{ number_format($total_disc, 2) }}</td>
              <td align="right">{{ number_format($sub_total, 2) }}</td>
            </tr>
          @endforeach
          <tr>
            <td colspan="3" align="center"><b>Total:</b></td>
            <td align="center"><b>{{ number_format($tot_qty, 2) }}</b></td>
            <td align="right"><b>{{ number_format($tot_sub_amount, 2) }}</b></td>
            <td align="center"><b>{{ number_format($tot_disc, 2) }}</b></td>
            <td align="right"><b>{{ number_format($tot_total_disc, 2) }}</b></td>
            <td align="right"><b>{{ number_format($tot_sub_amount - $tot_total_disc , 2) }}</b></td>
          </tr>

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
