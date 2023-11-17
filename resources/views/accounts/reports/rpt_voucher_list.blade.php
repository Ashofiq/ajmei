@extends('layouts.app')
@section('css')

@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Voucher Report</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id='myform' action="{{route('rpt.vh.list')}}" method="POST">
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
                    <option {{ $default_comp_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>
    <div class="col-md-2">
       <div class="input-group mb-2"> 
         <select class="form-control m-bot15" name="voucher_type" >
           <option value="" >--Select Voucher Type--</option>
            @if ($acc_doctypes->count())
                @foreach($acc_doctypes as $doctype)
                    <option {{ $voucher_type == $doctype->doc_type ? 'selected' : '' }} value="{{$doctype->doc_type}}" >{{ $doctype->trans_type }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "15" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
          <!-- input type="date" name="fromdate" id="fromdate" value="{{$fromdate}}" class="form-control" placeholder="dd/mm/YYYY" required/ -->
       </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="text" size = "15" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
          <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

          <!-- input type="date" name="todate" id="todate" value="{{ old('todate') == "" ? $todate : old('todate') }}" class="form-control" placeholder="To Date" required/ -->
       </div>
    </div>
    <div class="col-md-2">
        <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        &nbsp;<button type="submit" name="submit" value='pdf' id='btn2' class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
    </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-light">
          <tr>
            <th class="text-center">Voucher No</th>
            <th class="text-center">Account Name</th>
            <th class="text-center">Narration</th>
            <th class="text-center">Debit</th>
            <th class="text-center">Credit</th>
          </tr>
        </thead>
        <tbody><?php $dt = ''; $total_Debit = 0;   $total_Credit = 0; ?>
          @foreach($vouchers as $row)
          <?php
          $total_Debit = $total_Debit + $row->d_amount;
          $total_Credit = $total_Credit + $row->c_amount;
          if($dt == '' || $dt != $row->voucher_date){ ?>
            <tr>
              <td colspan="5"><b>{{ date('d/m/Y', strtotime($row->voucher_date)) }}</b></td>
            </tr>
          <?php } ?>
          <tr>
            <td width="8%">{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            <td width="20%">{{ $row->acc_head }}</td>
            <td width="20%">{{$row->trans_type == 'SV'?'Invoice-':''}}{{$row->acc_invoice_no}} {!! $row->t_narration !!}</td>
            <td width="10%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'':number_format($row->d_amount,2) }}</td>
            <td width="10%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'':number_format($row->c_amount,2) }}</td>
          </tr>
          <?php $dt = $row->voucher_date; ?>
          @endforeach
          <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td width="10%" align="right"><b>{{ number_format($total_Debit,2) }}</b></td>
            <td width="10%" align="right"><b>{{ number_format($total_Credit,2) }}</b></td>
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
