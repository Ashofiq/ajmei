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
        <font size="3" color="blue"><b>Delivery Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('rpt.del.list')}}" method="POST">
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
    <div class="col-md-2">
      <input type="text" name="del_no" id="del_no" value="{{$del_no}}" class="form-control" placeholder="Enter Delivery No"/>
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
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Delivery&nbsp;Date</th>
          <th class="text-center" scope="col">Delivery No</th>
          <th class="text-center" scope="col">Order Ref</th>
          <th class="text-center" scope="col">Customer</th>
          <!--th class="text-center" scope="col">Delivered To</th -->
          <th class="text-center" scope="col">Delivered Address</th>
          <th class="text-center" scope="col">Delivered&nbsp;Phone</th>
          <th class="text-center" scope="col">Total Amount</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Total VAT</th>
          <th class="text-center" scope="col">Net Amount</th>
          <th class="text-center" colspan="2">Options</th>
        </thead>
        <tbody>
          <?php  
            $del_sub_total = 0;
            $del_total_disc = 0; 
            $del_vat_value = 0;
            $del_net_amt = 0;
         ?>
          @foreach($rows as $row)
          <?php 
            $del_sub_total += $row->del_sub_total;
            $del_total_disc += $row->del_total_disc; 
            $del_vat_value += $row->del_vat_value;
            $del_net_amt += $row->del_net_amt;
            
          ?>
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->del_date }}</td>
            <td>{{ $row->del_no }}</td>
            <td>{{ $row->del_po_no }}</td>
            <td>{{ $row->cust_name }}</td>
            <!-- td>{{ $row->del_customer }}</td -->
            <td>{{ $row->del_add }}</td>
            <td>{{ $row->del_cont_no }}</td>
            <td align="right">{{ $row->del_sub_total }}</td>
            <td align="right">{{ $row->del_total_disc }}</td>
            <td align="right">{{ $row->del_vat_value }}</td>
            <td align="right">{{ $row->del_net_amt }}</td>
            <td>
              <div class="btn-group btn-corner">
                <a href="{{ route('delivery.challan.pdf',$row->id) }}" class="btn btn-xs btn-primary" title="Delivery Challan" target="_blank">Challan</a>
                <a href="{{ route('delivery.invoice.pdf',$row->id) }}" class="btn btn-xs btn-info" title="Invoice" target="_blank">Invoice</a>
              </div>
            </td>
          </tr>
          @endforeach
           <tr>
            <td colspan="6">&nbsp;</td> 
            <td align="right"><b>{{ number_format($del_sub_total,2) }}</b></td>
            <td align="right"><b>{{ number_format($del_total_disc,2) }}</b></td>
            <td align="right"><b>{{ number_format($del_vat_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($del_net_amt,2) }}</b></td>
            <td>&nbsp;</td>
          </tr>
          </tbody>
        </table>
        </div>

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
