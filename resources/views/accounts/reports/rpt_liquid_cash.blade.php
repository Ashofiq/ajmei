@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Liquid Cash Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.liquid.cash.sheet')}}" id="acc_form" method="post">
      {{ csrf_field() }}
      <div class="row justify-content-center">
        <div class="col-md-2">
           <div class="input-group mb-2">
             <select class="form-control m-bot15" id="company_code" name="company_code" required>
               <option value="" >--Select Company--</option>
                @if ($companies->count())
                    @foreach($companies as $company)
                        <option {{ $default_comp_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
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
        <div class="col-md-12">
          <table class="table table-striped table-data table-view">
            <thead class="thead-light">
              <tr>
                <th class="text-center">Account Name</th>
                <th class="text-center" colspan="2">Balance</th>
              </tr>
              <tr>
                <th class="text-center" width="55%"></th>
                <th class="text-center" width="13%">Debit</th>
                <th class="text-center" width="13%">Credit</th>
              </tr>
            </thead>
            <tbody>
             <?php
              $total_op = 0;   $total_bal = 0;
              $total_tr_debit = 0;
              $total_tr_credit = 0;

              $total_bal_debit = 0;
              $total_bal_credit = 0;

             ?>
              @foreach($rows as $row)
              <?php
                $op  = $row->op_debit - $row->op_credit;
                $bal = $op + $row->tr_debit -  $row->tr_credit;

                $total_op = $total_op + $op;
                $total_bal  = $total_bal + $bal;

                $total_tr_debit = $total_tr_debit + $row->tr_debit;
                $total_tr_credit = $total_tr_credit + $row->tr_credit;

                if($bal>0) $total_bal_debit = $total_bal_debit + $bal;
                if($bal<0) $total_bal_credit = $total_bal_credit + $bal;

                ?>
              <tr>
                 <td>{{ $row->acc_head }}</td>
                 <td align="right">{{ $bal > 0 ? number_format($bal,2):'0.00'}}</td>
                 <td align="right">{{ $bal < 0 ? number_format(abs($bal),2) :'0.00'}}</td>
              </tr>

              @endforeach
              <tr>
                <td align="right"><b>Total:</b></td>
                <td align="right"><b>{{ number_format($total_bal_debit,2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($total_bal_credit),2) }}</b></td>
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
 </script>
@stop
