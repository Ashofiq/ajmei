@extends('layouts.app_m')
@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Sale Date:</dt><dd>&nbsp;{{ $rows_m->del_date }}</dd>
      <dt>Customer Name:</dt><dd>&nbsp;{{ $rows_m->cust_name }}</dd>
      <dt>Customer Phone: </dt><dd>&nbsp;{{ $rows_m->cust_mobile }}</dd>
      <dt>Address:</dt><dd>&nbsp;{{ $rows_m->cust_add1 }} {{ $rows_m->cust_add2 }}</dd>
    </dl>
  </div>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <?php $misc_cost =  $rows_m->del_carring_cost + $rows_m->del_labour_cost + $rows_m->del_load_unload_cost + $rows_m->del_service_charge + $rows_m->del_other_cost; ?>
      <dt>Delivery No:</dt><dd>&nbsp;{{ $rows_m->del_no }}</dd>
      <dt>Total Bill:</dt><dd>&nbsp;{{ number_format($rows_m->del_sub_total + $rows_m->del_total_disc,0) }}</dd> 
      <dt>Total Discount:</dt><dd>&nbsp;{{ number_format($rows_m->del_total_disc,0) }}</dd>
      <dt>Total Misc Cost:</dt><dd>&nbsp;{{ number_format($misc_cost,0) }} </dd>  
      <dt>Net Total:</dt><dd>&nbsp;{{ number_format($rows_m->del_net_amt + $misc_cost,0) }}</dd>
    </dl>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item&nbsp;Code</th>
            <th>Item&nbsp;Name</th>  
            <td>Size</td>
            <td>Per Pcs Weight (GM)</td>
            <td>Qty (Pcs)</td> 
            <td>Total Weight (KG)</td>
            <th>Sales Unit</th>
            <th>Sales Price</th>
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

            if($sale_details->del_item_unit == 'KG'){ 
              $sub_amount = $sale_details->del_item_price*$sale_details->del_qty;
            }else{
              $sub_amount = $sale_details->del_item_price*$sale_details->del_item_pcs;
            }

            
            $total_disc = ($sub_amount*$sale_details->del_disc)/100;
            $sub_total = $sub_amount - $total_disc;

            $tot_disc       += $sale_details->del_disc;
            $tot_qty        += $sale_details->del_qty;
            $tot_sub_amount += $sub_amount;
            $tot_total_disc += $total_disc;

            ?>
            <tr>
              <td>{{ $sale_details->item_code }}</td>
              <td>{{ $sale_details->item_name }}({{ $sale_details->itm_cat_name }})</td>
              <td>{{ $sale_details->del_item_size }}</td>
              <td>{{ $sale_details->del_item_weight }}</td>
              <td>{{ $sale_details->del_item_pcs }}</td> 
              <td align="center">{{ $sale_details->del_qty }}</td>
              <td>{{ $sale_details->del_item_unit }}</td> 
              <td align="center">{{ $sale_details->del_item_price }}</td>
              <td align="right">{{ number_format($sub_amount, 2) }}</td>
              <td align="center">{{ number_format($sale_details->del_disc, 2) }}</td>
              <td align="right">{{ number_format($total_disc, 2) }}</td>
              <td align="right">{{ number_format($sub_total, 2) }}</td>
            </tr>
          @endforeach
          <tr>
            <td colspan="5" align="center"><b>Total:</b></td>
            <td align="center"><b>{{ number_format($tot_qty, 2) }}</b></td>
            <td colspan="2" align="center">&nbsp;</td>
            <td align="right"><b>{{ number_format($tot_sub_amount, 2) }}</b></td>
            <td align="center"><b>{{ number_format($tot_disc, 2) }}</b></td>
            <td align="right"><b>{{ number_format($tot_total_disc, 2) }}</b></td>
            <td align="right"><b>{{ number_format($tot_sub_amount - $tot_total_disc , 2) }}</b></td>
          </tr>
          
          <tr>
            <td colspan="11" align="right"><b>Total Discount:</b></td> 
            <td align="right"><b>{{ number_format($tot_total_disc, 0) }}</b></td>
          </tr>

          <tr>
            <td colspan="11" align="right"><b>Carring Cost:</b></td> 
            <td align="right"><b>{{ number_format($rows_m->del_carring_cost, 0) }}</b></td>
          </tr>
          <tr>
            <td colspan="11" align="right"><b>Labour Cost:</b></td> 
            <td align="right"><b>{{ number_format($rows_m->del_labour_cost, 0) }}</b></td>
          </tr>
          <tr>
            <td colspan="11" align="right"><b>Service Charge:</b></td> 
            <td align="right"><b>{{ number_format($rows_m->del_service_charge, 0) }}</b></td>
          </tr>
          <tr>
            <td colspan="11" align="right"><b>Other Cost:</b></td> 
            <td align="right"><b>{{ number_format($rows_m->del_other_cost, 0) }}</b></td>
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
