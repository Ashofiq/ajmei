@extends('layouts.app')
@section('css')

@stop
@section('content')


<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Date Wise Sales Summary Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id="myform" action="{{route('rpt.inv.date.summ.list')}}" method="POST">
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
      <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info">Search</button>
      &nbsp;<button type="submit" name="submit" value='pdf' id='btn2' class="btn btn-sm btn-info">PDF</button>
    </div>

  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-8">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Invoice&nbsp;Date</th>
          <th class="text-center" scope="col">Total Amount</th>
          <th class="text-center" scope="col">Total Discount</th>
          <th class="text-center" scope="col">Total VAT</th>
          <th class="text-center" scope="col">Net Amount</th>
        </thead>
        <tbody>
          <?php $inv_sub_total = 0; $inv_disc_value = 0;
          $inv_vat_value = 0; $inv_net_amt = 0; ?>
          @foreach($rows as $row)
          <?php $inv_sub_total += $row->inv_sub_total;
          $inv_disc_value += $row->inv_itm_disc_value + $row->inv_disc_value;
          $inv_vat_value += $row->inv_vat_value;
          $inv_net_amt += $row->inv_net_amt; ?>
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td align="right">{{ date('d-m-Y', strtotime($row->inv_date)) }}</td>
            <td align="right">{{ number_format($row->inv_sub_total,2) }}</td>
            <td align="right">{{ number_format($row->inv_itm_disc_value + $row->inv_disc_value,2) }}</td>
            <td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td align="right"><b>Total</b></td>
            <td align="right"><b>{{ number_format($inv_sub_total,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
            <td align="right"><b><b>{{ number_format($inv_net_amt,2) }}</b></td>
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
</script>
@stop
