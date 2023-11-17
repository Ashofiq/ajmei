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
        <font size="3" color="blue"><b>Sales Persons Wise Sales Summary</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id="myform" action="{{route('rpt.salesperson.wise.sales.stm')}}" method="POST">
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
        <button type="submit" name="submit" value='html' id="btn1" class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        <button type="submit" name="submit" value='pdf' id="btn2" data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
    </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Sales Person</th>
          <th class="text-center" scope="col">Total Sales</th>
          <th class="text-center" scope="col">Received</th>
          <th class="text-center" scope="col">VAT</th>
          <th class="text-center" scope="col">Outstanding</th>
        </thead>
        <tbody>
            <?php
            $inv_net_amt = 0;
            $collection = 0;
            $inv_vat_value = 0;
            $outstanding = 0;
         ?>
          @foreach($rows as $row)
          <?php

            $inv_net_amt += $row->inv_net_amt;
            $collection += $row->collection;
            $inv_vat_value += $row->inv_vat_value;
            $outstanding += $row->outstanding;

          ?>
          <tr>
            <td style=display:none;></td>
            <td>{{ $row->sales_name }}</td>
            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
            <td align="right">{{ number_format($row->collection,2) }}</td>
            <td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
            <td align="right">{{ number_format($row->outstanding,2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="1"><b>Total:</b></td>
            <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
            <td align="right"><b>{{ number_format($collection,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($outstanding,2) }}</b></td>
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
