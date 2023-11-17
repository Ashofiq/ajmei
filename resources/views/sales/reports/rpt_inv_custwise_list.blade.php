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
        <font size="3" color="blue"><b>Customer Wise Sales Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('rpt.inv.cust.list')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-1">
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
    <div class="col-md-3">
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
        <button type="submit" name="submit" value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        &nbsp;
        <button type="submit" name="submit" value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
    </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Invoice&nbsp;Date</th>
          <th class="text-center" scope="col">Invoice No</th>
          <th class="text-center" scope="col">Customer</th>
          <th class="text-center" scope="col">Address</th>
          <th class="text-center" scope="col">Item Name</th>
          <th class="text-center" scope="col">LotNo</th>
          <th class="text-center" scope="col">Rate</th>
          <th class="text-center" scope="col">Qty</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Net Amount</th>
        </thead>
        <tbody>
         <?php 
          
            $inv_qty = 0;
            $inv_disc_value = 0; 
            $inv_net_amt = 0;
         ?>
          @foreach($rows as $row)
          <?php 
            $inv_qty += $row->inv_qty;
            $inv_disc_value += $row->inv_disc_value; 
            $inv_net_amt += $row->inv_net_amt;
            
          ?>
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->inv_date }}</td>
            <td>{{ $row->inv_no }}</td>
            <td>{{ $row->cust_name }}</td>
            <td>{{ $row->cust_add1 }} {{ $row->cust_add2 }}</td>
            <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td>
            <td>{{ $row->inv_lot_no }}</td>
            <td>{{ $row->inv_item_price }}</td>
            <td align="right">{{ $row->inv_qty }}</td>
            <td align="right">{{ $row->inv_disc_value }}</td>
            <td align="right">{{ $row->inv_net_amt }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="7">&nbsp;</td> 
            <td align="right"><b>{{ number_format($inv_qty,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
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
