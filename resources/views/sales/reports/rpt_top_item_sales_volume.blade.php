@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')

<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>To 20 Item Sales By Volume Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id='myform' action="{{route('rpt.top.item.sales.volume')}}" method="POST">
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
    <div class="col-md-4">
      <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info">Search</button>
      &nbsp;<button type="submit" name="submit" value='pdf' id='btn2' class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
  </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-8">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th class="text-center" scope="col">SL No</th>
          <th class="text-center" scope="col">Item Name</th>
          <th class="text-center" scope="col">Sales Volume</th>
          <th class="text-center" scope="col">Sales Qty</th>
        </thead>
        <tbody>
         <?php
            $i=1;
            $total_qty = 0;
            $total_volume = 0;
         ?>
          @foreach($rows as $row)
          <?php
            $total_qty += $row->inv_qty;
            $total_volume += $row->inv_net_amt;
          ?>
          <tr>
            <td align="center">{{$i++}}</td>
            <td>{{ $row->item_name }}</td>
            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
            <td align="right">{{ number_format($row->inv_qty,2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="2" align="center"><b>Total</b></td>
            <td align="right"><b>{{ number_format($total_volume,2) }}</b></td>
            <td align="right"><b>{{ number_format($total_qty,2) }}</b></td>
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
  document.getElementById('btn1').onclick = function() {
    form.submit();
  }

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
