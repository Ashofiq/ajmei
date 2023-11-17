@extends('layouts.app')
@section('css')

@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal --> 

<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Sales Order List (Pending)</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('rpt.sales.order.list.pending')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-3">
       <div class="input-group mb-2">
         <div class="input-group-prepend">
           <span class="input-group-text">Company Code&nbsp;:</span>
         </div>
         <select class="form-control m-bot15" name="company_code" required>
           <option value="" >--Select--</option>
            @if ($companies->count())
                @foreach($companies as $company)
                    <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>

    <div class="col-md-3">
      <div class="input-group">
          <div class="input-group ">
              <select name="customer_id" class="col-md-12 chosen-select" id="customer_id">
                  <option value="" disabled selected>- Select Customer -</option>
                  @foreach($customers as $customer)
                      <option {{ $customer_id == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">AJC-000{{ $customer->cust_slno }} -> {{ $customer->cust_name }}</option>
                  @endforeach
              </select>
              @error('customer_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>
      </div>
    </div>
   
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "15" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "15" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div>
    <div class="col-md-2">
        <button type="submit" name="submit" value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        &nbsp;
        <button type="submit" name="submit" value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
    </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">

        <table class="table table-striped table-view">
            <thead class="thead-blue">
                <th class="text-center" scope="col">#</th>
                <th class="text-center" scope="col">Order No</th>
                <th class="text-center" scope="col">Order Date</th>
                <th class="text-center" scope="col">Party Name</th>
                <th class="text-center" scope="col">FPO No</th>
                <th class="text-center" scope="col">FPO Date</th>
                <th class="text-center" scope="col">Exp Del Date</th>
                <th class="text-center" scope="col">Description</th>
                <th class="text-center" scope="col">Size</th>
                <th class="text-center" scope="col">Per Pcs Weight</th>
                <th class="text-center" scope="col">Order qty(pcs)</th>
                <th class="text-center" scope="col">Delivery(pcs)</th>
                <th class="text-center" scope="col">Return(pcs)</th>
                <th class="text-center" scope="col">Pending(pcs)</th>
            </thead>
            <tbody>
              <?php 
                $orderPcs = 0;
                $del_item_pcs = 0;
                $return_pcs = 0;
                $pending_pcs = 0;
              ?>
                @foreach($pendingItems as $key => $row)
                    <?php 
                      $orderPcs += $row->orderPcs;
                    ?>
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center">{{ $row->so_order_no }}</td>
                    <td class="text-center">{{ $row->so_order_date }}</td>
                    <td class="text-center">{{ $row->customer->cust_name }}</td>
                    <td class="text-center">000{{ $row->so_fpo_no }}</td>
                    <td class="text-center">{{ $row->so_confirmed_date }}</td>
                    <td class="text-center">{{ $row->so_req_del_date }}</td>
                    <td class="text-center"> </td>
                    <td class="text-center"> </td>
                    <td class="text-center"> </td>
                    <td class="text-center"> </td>
                    <td class="text-center"> </td>
                    <td class="text-center"> </td>
                    <td class="text-center"> </td>
                </tr>

                  @foreach($row->items as $key => $item)
                  <?php 
                    $delQty = Helper::getDeliveryPcs($row->id, $item->so_item_id);
                    $retQty = Helper::getDeliveryReturnPcs($row->id, $item->so_item_id);
                  ?>
                   <?php 
                      $orderPcs += $item->so_item_pcs;
                      $del_item_pcs += $delQty;
                      $return_pcs += $retQty;
                      $pending_pcs += abs(($item->so_item_pcs - $delQty) + $retQty);
                    ?>
                  <tr>
                      <td class="text-center" colspan="8">{{ $item->so_item_spec }}</td>
                      <td class="text-center">{{ $item->so_item_size }}</td>
                      <td class="text-center">{{ $item->so_item_weight }}</td>
                      <td class="text-center">{{ $item->so_item_pcs }}</td>
                      <td class="text-center"> {{ $delQty }}</td>
                      <td class="text-center"> {{ $retQty }}</td>
                      <td class="text-center">{{ abs(($item->so_item_pcs - $delQty) + $retQty) }}</td>
                  </tr>
                  @endforeach
                @endforeach

                <tr>
                  <td colspan="10" align="right"><b>Total:</b></td>
                  <td align="right"><b>{{ $orderPcs }}</b></td>
                  <td align="right"><b>{{ $del_item_pcs }}</b></td>
                  <td align="right"><b>{{ $return_pcs }}</b></td>
                  <td align="right"><b>{{ $pending_pcs }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
</section>

@stop


@section('pagescript')
<script type="text/javascript">
  $(document).ready(function() {
// show modal
$('.viewModal').click(function(event) {
    event.preventDefault();

    var url = $(this).attr('href');
    //alert(url);
    $('#exampleModal').modal('show');
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'html',
    })
    .done(function(response) {
        $("#exampleModal").find('.modal-body').html(response);
    });
  });

});
</script>
@stop
