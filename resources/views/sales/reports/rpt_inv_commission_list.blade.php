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
        <font size="3" color="blue"><b>Sales Commission Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id='myform'  action="{{route('rpt.inv.comm.list')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-2">
       <div class="input-group mb-2"> 
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
       <div class="input-group mb-2">
           <div class="input-group ss-item-required">
               <select id="customer_id" name="customer_id" class="chosen-select" >
                 <option value="" selected>- Select Customer -</option>
                   @foreach($customers as $cust)
                     <option {{ $customer_id == $cust->id ? 'selected' : '' }} value="{{ $cust->id }}">{{ $cust->cust_name }}</option>
                   @endforeach
               </select>
               @error('customer_id')
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
          &nbsp;&nbsp;<input type="text" size = "8" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div>

    <div class="col-md-4">
      <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info">All</button>
      &nbsp;<button type="submit" name="submit" value='html_1' id='btn2' class="btn btn-sm btn-info">Paid</button>
      &nbsp;<button type="submit" name="submit" value='html_2' id='btn2' class="btn btn-sm btn-info">Pending</button>
      &nbsp;<button type="submit" name="submit" value='pdf' id='btn3' class="btn btn-sm btn-info"><span class="fa fa-search">PDF(Pending)</span></button>
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
          <th class="text-center" scope="col">Sales Voucher</th>
          <th class="text-center" scope="col">Customer</th>
          <th class="text-center" scope="col">Voucher</th>
          <th class="text-center" scope="col">Net Amount</th>
          <th class="text-center" scope="col">Commission</th>
        </thead>
        <tbody>
         <?php
            $total_invnetamt = 0;
            $total_invcomm = 0;
         ?>
          @foreach($rows as $row)
          <?php
            $total_invnetamt += $row->inv_netamt;
            $total_invcomm += $row->commission;
          ?>
          <tr>
            <td>{{date('d-m-y',strtotime($row->inv_date))}}</td>
            <td>{{ $row->inv_no }}</td>
            <td>{{ $row->trans_type }} {{ $row->voucher_no }}</td>
            <td>{{ $row->cust_name }}</td>
            <td>{{ $row->inv_acc_voucher }}</td>
            <td align="right">{{ number_format($row->inv_netamt,2) }}</td>
            <td align="right">{{ number_format($row->commission,2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="5">&nbsp;</td>
            <td align="right"><b>{{ number_format($total_invnetamt,2) }}</b></td>
            <td align="right"><b>{{ number_format($total_invcomm,2) }}</b></td>
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
 var form = document.getElementById('myform');
  document.getElementById('btn3').onclick = function() {
    form.target = '_blank';
    form.submit();
  }
  
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
