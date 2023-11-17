@extends('layouts.app_m')
@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Date:</dt><dd>&nbsp;{{ date('d-m-Y',strtotime($rows_m->short_date)) }}</dd>
      <dt>Comments:</dt><dd>&nbsp;{{ $rows_m->short_comments }}</dd>
    </dl>
  </div>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Total Qty:</dt><dd>&nbsp;{{ $rows_m->short_total_qty }}</dd>
    </dl>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item&nbsp;Code</th>
            <th>Item&nbsp;Name</th>
            <th>Remarks</th> 
            <th>Quantity</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
            @foreach($rows_d as $p_details)
            <tr>
              <td>{{ $p_details->item_code }}</td>
              <td>{{ $p_details->item_name }}({{ $p_details->itm_cat_name }})</td>
              <td>{{ $p_details->short_item_remarks }}</td> 
              <td align="right">{{ $p_details->short_item_qty }}</td>
              <td align="center">{{ $p_details->short_item_unit }}</td>
              <td align="center">{{ $p_details->short_item_price }}</td>
              <td align="right">{{ number_format($p_details->short_item_qty*$p_details->short_item_price,2) }}</td>
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
