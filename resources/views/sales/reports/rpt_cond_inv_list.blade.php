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
        <font size="3" color="blue"><b>Sales Conditional Report :: {{$tag}}</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id="myform" action="{{route('rpt.cond.inv.list')}}" method="POST">
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
    <div class="col-md-2">
      <input type="text" name="inv_no" id="inv_no" value="{{$inv_no}}" class="form-control" placeholder="Enter Invoice No"/>
    </div>
    <div class="col-md-1.5">
        <div class="form-group">
          <input type="text" size = "8" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "8" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
        </div>
    </div>
    <div class="col-md-2">
         <select class="form-control" name="report_type" id="report_type" required>
           <option value="all" {{ $report_type == 'all' ? 'selected' : '' }}>ALL</option>
           <option value="paid" {{ $report_type == 'paid' ? 'selected' : '' }}>PAID</option>
           <option value="pending" {{ $report_type == 'pending' ? 'selected' : '' }}>PENDING</option>
        </select>
    </div>
    <div class="col-md-2">
      <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info">View</button>
      &nbsp;<button type="submit" name="submit" value='pdf' id='btn2' class="btn btn-sm btn-info">PDF</button>
    </div>

  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th class="text-center" scope="col">Date</th>
          <th class="text-center" scope="col">Invoice No</th>
          <th class="text-center" scope="col">Ref No</th>
          <th class="text-center" scope="col">Customer</th>
          <th class="text-center" scope="col">Delivery To</th>
          <th class="text-center" scope="col">Voucher No</th>
          <th class="text-center" scope="col">Comments</th>
          <th class="text-center" scope="col">Total Amount</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Total VAT</th>
          <th class="text-center" scope="col">Net Amount</th>
        </thead>
        <tbody>
         <?php
            $inv_sub_total  = 0;
            $inv_disc_value = 0;
            $inv_vat_value  = 0;
            $inv_net_amt    = 0;
         ?>
          @foreach($rows as $row)
           <?php
            $inv_sub_total  += $row->inv_sub_total;
            $inv_disc_value += $row->inv_disc_value;
            $inv_vat_value  += $row->inv_vat_value;
            $inv_net_amt    += $row->inv_net_amt;
          ?>
          <tr>
            <td>{{ date('d-m-Y',strtotime($row->inv_date)) }}</td>
            <td>{{ $row->inv_no }}</td>
            <td>{{ $row->inv_del_ref }}</td>
            <td>{{ $row->cust_name }}</td>
            <td>{{ $row->courrier_to }}</td>
            <td>{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            <td>{{ $row->inv_del_comments }}</td>
            <td align="right">{{ $row->inv_sub_total }}</td>
            <td align="right">{{ $row->inv_disc_value }}</td>
            <td align="right">{{ $row->inv_vat_value }}</td>
            <td align="right">{{ $row->inv_net_amt }}</td>

          </tr>
          @endforeach
          <tr>
            <td colspan="7">&nbsp;</td>
            <td align="right"><b>{{ number_format($inv_sub_total,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></b></td>
            <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
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

  var form = document.getElementById('myform');
    document.getElementById('btn2').onclick = function() {
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
