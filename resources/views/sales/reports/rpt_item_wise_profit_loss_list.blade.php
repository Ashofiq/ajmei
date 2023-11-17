@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
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
        <font size="3" color="blue"><b>Item Wise Profit & Loss Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('rpt.item.wise.profit_loss')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-2">
       <div class="input-group mb-2">
         <select class="form-control m-bot15" name="company_code" required>
           <option value="" >--Select Company--</option>
            @if ($companies->count())
                @foreach($companies as $company)
                    <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>
    <div class="col-md-4">
       <div class="input-group mb-2">
           <div class="input-group ss-item-required">
               <select id="customer_id" name="customer_id" class="chosen-select" >
                 <option value="" disabled selected>- Select Customer -</option>
                   @foreach($customers as $cust)
                     <option {{ old('customer_id') == $cust->id ? 'selected' : '' }} value="{{ $cust->id }}">{{ $cust->cust_name }}</option>
                   @endforeach
               </select>
               @error('customer_id')
               <span class="text-danger">{{ $message }}</span>
               @enderror
           </div>
         </div>
    </div>
    <div class="col-md-3">
       <div class="input-group mb-2">
           <div class="input-group ss-item-required">
               <select id="item_id" name="item_id" class="chosen-select" >
                 <option value="" disabled selected>- Select Item -</option>
                   @foreach($item_list as $item)
                     <option {{ old('item_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->item_name }}({{$item->itm_cat_origin}})</option>
                   @endforeach
               </select>
               @error('item_id')
               <span class="text-danger">{{ $message }}</span>
               @enderror
           </div>
         </div>
    </div>
    <div class="col-md-1.5">
        <div class="form-group">
          <input type="text" size = "8" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div>
    <div class="col-md-1.5">
        <div class="form-group">
          <input type="text" size = "8" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div> 
    <div class="col-md-2">
      <div class="input-group ss-item-required">
        <div class="input-group-prepend ">
          <div class="input-group-text" style="min-width:50px">Overhead Cost:</div>
          </div>
            <input type="text" name="overheadCost" value="{{ $overheadCost }}" class="form-control  input-sm" autocomplete="off" />
        </div>
    </div>
    <div class="col-md-2">
      <div class="input-group ss-item-required">
        <div class="input-group-prepend ">
          <div class="input-group-text" style="min-width:50px">Freight And Others:</div>
          </div>
            <input type="text" name="freightOthers" value="{{ $freightOthers }}" class="form-control  input-sm" autocomplete="off" />
        </div>
    </div>
    
    <div class="col-md-1">
        <button type="submit" name="submit" value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        &nbsp;<!-- button type="submit" name="submit" value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button-->
    </div>
  </div>
  </form>


  <div class="row justify-content-left">
    <div class="col-md-12"> 
      <table style='font-size:80%' class="table table-striped table-data table-view">
        <thead class="thead-blue"> 
          <th class="text-center" scope="col">Item Name</th> 
          <th class="text-center" scope="col">Rate</th>
          <th class="text-center" scope="col">Qty</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Net Amount</th>
          <th class="text-center" scope="col">Purchase Price(Avg)</th>
          <th class="text-center" scope="col">Overhead Cost</th>
          <th class="text-center" scope="col">Freight And Others</th>
          <th class="text-center" scope="col">Sales Comm </th>
          <th class="text-center" scope="col">Sales Comm(/Unit) </th>
          <th class="text-center" scope="col">Total Unit Cost</th>
          <th class="text-center" scope="col">Total Cost</th>
          <th class="text-center" scope="col">Net Profit</th>
          <th class="text-center" scope="col">Profit Ration(%)</th>

        </thead>
        <tbody>
         <?php 
          
            $inv_qty = 0;
            $inv_disc_value = 0; 
            $inv_net_amt = 0;
            $inv_comm = 0;

            $gr_unit_comm = 0;
            $gr_overheadCost = 0;
            $gr_freightOthers = 0; 
            $gr_total_cost = 0;
            $gr_total_unit_cost = 0;
            $gr_net_profit = 0;  
         ?>
          @foreach($rows as $row)
          <?php 
            $inv_qty += $row->inv_qty;
            $inv_disc_value += $row->inv_disc_value; 
            $inv_net_amt += $row->inv_net_amt;
            $inv_comm += $row->commission;

            $unit_comm  = 0;
            if($row->inv_qty > 0) {
              $unit_comm = $row->commission/$row->inv_qty;
            }
            $gr_unit_comm += $unit_comm;

            $t_overheadCost = $row->l_item_avg_price*$overheadCost/100;
            $t_freightOthers = $row->l_item_avg_price*$freightOthers/100;
            $total_unit_cost =  $row->l_item_avg_price + $t_overheadCost + $t_freightOthers + $unit_comm;
            $total_cost  = 0;
            if($row->inv_qty > 0) {
              $total_cost = $total_unit_cost*$row->inv_qty;
            } 
            $net_profit = $row->inv_net_amt - $total_cost;
            $profitration  = 0;
            if($row->inv_net_amt > 0) {
              $profitration = ($net_profit*100)/$row->inv_net_amt;
            }
 
            $gr_overheadCost += $t_overheadCost;
            $gr_freightOthers += $t_freightOthers; 
            $gr_total_unit_cost += $total_unit_cost;
            $gr_total_cost += $total_cost;
            $gr_net_profit += $net_profit;  
          ?>
          <tr> 
            <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td> 
            <td>{{ number_format($row->inv_item_price,2) }}</td>
            <td align="right">{{ $row->inv_qty }}</td>
            <td align="right">{{ number_format($row->inv_disc_value,2) }}</td>
            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
            <td align="right">{{ number_format($row->l_item_avg_price,2) }}</td>
            <td align="right">{{ number_format($t_overheadCost,2) }}</td>
            <td align="right">{{ number_format($t_freightOthers,2) }}</td>
            <td align="right">{{ number_format($row->commission,2) }}</td>
            <td align="right">{{ number_format($unit_comm,2) }}</td> 
            <td align="right">{{ number_format($total_unit_cost,2) }}</td>
            <td align="right">{{ number_format($total_cost,2) }}</td>
            <td align="right">{{ number_format($net_profit,2) }}</td>
            <td align="right">{{ number_format($profitration,2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="2">&nbsp;</td> 
            <td align="right"><b>{{ number_format($inv_qty,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
            <td align="right"><b>&nbsp;</b></td>
            <td align="right"><b>{{ number_format($gr_overheadCost,2) }}</b></td>
            <td align="right"><b>{{ number_format($gr_freightOthers,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_comm,2) }}</b></td>
            <td align="right"><b>{{ number_format($gr_unit_comm,2) }}</b></td>
            <td align="right"><b>{{ number_format($gr_total_unit_cost,2) }}</b></td>
            <td align="right"><b>{{ number_format($gr_total_cost,2) }}</b></td>
            <td align="right"><b>{{ number_format($gr_net_profit,2) }}</b></td>
            <td align="right"><b>
              @if($inv_net_amt > 0)
              {{ number_format($gr_net_profit*100/$inv_net_amt,2) }}
              @endif
            </b></td>

          </tr>
          </tbody>
        </table>
        </div>

    </div>

</div>
</section>

@stop


@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>
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
