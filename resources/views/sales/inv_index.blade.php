@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
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
        <font size="3" color="blue"><b> Invoice Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('sales.invoice.index')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-2">
       <div class="input-group"> 
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
        <div class="input-group ss-item-required">
            <div class="input-group ">
                <select name="customer_id" class="col-xs-6 col-sm-4 chosen-select" id="customer_id" onchange="customer()">
                    <option value="" disabled selected>- Select Customer -</option>
                    @foreach($customers as $customer)
                        <option {{ old('customer_id') == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">AJC-000{{ $customer->cust_slno }} -> {{ $customer->cust_name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
       </div>
    </div>
   <div class="col-md-2">
        <div class="input-group ss-item-required">
                <select name="inv_no" class="chosen-select" id="inv_no">
                    <option value="" disabled selected>- Invoice No -</option>
                    @foreach($invoices as $inv)
                        <option {{ old('inv_no') == $inv->inv_no ? 'selected' : '' }} value="{{ $inv->inv_no }}">{{ $inv->inv_no }}</option>
                    @endforeach
                </select>
                @error('inv_no')
                <span class="text-danger">{{ $message }}</span>
                @enderror
       </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "15" name="fromdate" onclick="displayDatePicker('fromdate');"  value="{{ $fromdate!=''?date('d-m-Y',strtotime($fromdate)):'' }}" />
          <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
       </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "15" name="todate" onclick="displayDatePicker('todate');"  value="{{ $fromdate!=''?date('d-m-Y',strtotime($todate)):'' }}" />
          <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div> 
    <div class="col-md-1">
        <button type="submit" name="submit" value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        &nbsp;<!-- button type="submit" name="submit" value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button-->
    </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Sales Order</th>
          <th class="text-center" scope="col">Invoice Date</th>
          <th class="text-center" scope="col">Invoice No</th>
          <th class="text-center" scope="col">Customer</th>
          <th class="text-center" scope="col">Total Amount</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Total VAT</th>
          <th class="text-center" scope="col">Net Amount</th>
          <th class="text-center" scope="col">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->so_order_no }}</td>
            <td>{{ date('d-m-Y',strtotime($row->inv_date)) }}</td>
            <td>{{ $row->inv_no }}</td>
            <td>{{ $row->cust_name }}</td>
            <td>{{ $row->inv_sub_total }}</td>
            <td>{{ $row->inv_disc_value }}</td>
            <td>{{ $row->inv_vat_value }}</td>
            <td>{{ $row->inv_net_amt }}</td>
            <td>
              <div class="btn-group btn-corner">
                <span href="{{ url('/') }}/invoice/sales-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}" class="btn btn-sm btn-info viewModal" 
                title="View Details" data-placement="top" >View
                </span>
                <a href="{{ route('sales.challan.pdf',$row->id) }}" class="btn btn-xs btn-primary" title="Delivery Challan" target="_blank">Challan</a>
                <a href="{{ route('sales.invoice.pdf',$row->id) }}" class="btn btn-xs btn-info" title="Invoice" target="_blank">Invoice</a>
                <span href="{{ url('/') }}/invoice/acc-m-view/{{$row->inv_no}}/{{$row->inv_fin_year_id}}" data-toggle="modal" data-id="{{$row->inv_no}}" class="btn btn-sm btn-success viewModal" title="View">
                    Acc.Doc
                </span>
                <a href="{{ route('sales.return.create',$row->id) }}" class="btn btn-xs">Return</a>
              </div>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
        </div>

    </div>
    <div class="col-md-12">
      <div class="card-tools">
          <ul class="pagination pagination-sm float-right">
            <p class="pull-right">
              {{ $rows->render("pagination::bootstrap-4") }}
            </p>
          </ul>
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
