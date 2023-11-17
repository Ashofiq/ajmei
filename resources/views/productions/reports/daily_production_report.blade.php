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
  <input type="hidden" name="menu_selection" id="menu_selection" value="CRM@1" class="form-control" required>

  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Daily Production Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('sales.prod.report.daily')}}" method="post">
  {{ csrf_field() }}
   <div class="row"> 
        <div class="col-md-2">
          <input type="text" name="order_no" id="order_no" value="{{old('order_no')}}" class="form-control" placeholder="Enter Order No"/>
        </div>
        <div class="col-md-4">
          <div class="input-group">
              <div class="input-group ">
                  <select name="customer_id" class="col-xs-6 col-sm-4 chosen-select" id="customer_id">
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
              <input type="text" size = "9" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
              <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
              <!-- input type="date" name="fromdate" id="fromdate" value="{{$fromdate}}" class="form-control" placeholder="dd/mm/YYYY" required/ -->
           </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
              <input type="text" size = "9" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
              <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
    
              <!-- input type="date" name="todate" id="todate" value="{{ old('todate') == "" ? $todate : old('todate') }}" class="form-control" placeholder="To Date" required/ -->
           </div>
        </div>
        
      <div class="col-md-2">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div>
  </div>
  </form>
  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th class="text-center" scope="col">#</th>
          <th class="text-center" scope="col">FPO No</th>
          <th class="text-center" scope="col">FPO Date</th>
          <th class="text-center" scope="col">Party</th>
          <th class="text-center" scope="col">Description</th>
          <th class="text-center" scope="col">size</th>
          <th class="text-center" scope="col">Weight Per Pcs.</th>
          <th class="text-center" scope="col">Order Pcs.</th>
          <th class="text-center" scope="col">Order Kg</th>
          <th class="text-center" scope="col">Previous Kg</th>
          <th class="text-center" scope="col">Previous Pcs.</th>
          <th class="text-center" scope="col">Today Prod KG</th>
          <th class="text-center" scope="col">Today Prod Pcs.</th>
          <th class="text-center" scope="col">Today Stock KG</th>
          <th class="text-center" scope="col">Today Stock Pcs.</th>
          <th class="text-center" scope="col">Balance KG</th>
          <th class="text-center" scope="col">Balance Pcs.</th>

        </thead>
        <tbody>
       
          @foreach($rows as $key => $row)
          <?php 
            $todayStockKg = $row->todayProdKg + $row->prevKg;
            $todayStockPcs = $row->todayProdPcs + $row->prevPcs;
          ?>
          <tr>
            <td> {{ $key + 1 }}</td>
            <td>000{{ $row->fpo }}</td>
            <td>{{ $row->fpoDate }}</td>
            <td>{{ $row->party }}</td>
            <td>{{ $row->spec }} </td>
            <td>{{ $row->size }} </td>
            <td>{{ $row->perPcsWeight }}</td>
            <td>{{ $row->orderPcs }}</td>
            <td>{{ $row->orderKg }}</td>
            <td>{{ $row->prevKg }}</td>
            <td>{{ $row->prevPcs }}</td>
            <td>{{ $row->todayProdKg }}</td>
            <td>{{ $row->todayProdPcs }}</td>
            <td>{{ $todayStockKg }}</td>
            <td>{{ $todayStockPcs }}</td>
            <td>{{ ($todayStockKg != 0) ? $row->orderKg - $todayStockKg : 0 }}</td>
            <td>{{ ($todayStockPcs != 0) ? $row->orderPcs - $todayStockPcs : 0 }}</td>
          </tr>
          @endforeach
         
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
