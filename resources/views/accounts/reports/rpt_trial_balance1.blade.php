@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Trial Balance</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.trial.bal1')}}" id="acc_form" method="post">
      {{ csrf_field() }}
      <div class="row justify-content-center">
        <div class="col-md-3">
           <div class="input-group mb-2">
             <div class="input-group-prepend">
               <span class="input-group-text">Company Code&nbsp;:</span>
             </div>
             <select class="form-control m-bot15" id="company_code" name="company_code" required>
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
          <button type="submit" name="submit" id='btn1'  value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
          &nbsp;<button type="submit" name="submit" id='btn2'  value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>

        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-report">
            <thead class="thead-light">
              <tr>
                <th class="text-center">Account Name</th>
                <!-- th class="text-center" colspan="2">Opening</th>
                <th class="text-center" colspan="2">Transaction</th -->
                <th class="text-center" colspan="2">Closing Balance</th>
              </tr>
              <tr>
                <th class="text-center"></th>
                <!--th class="text-center">Debit</th>
                <th class="text-center">Credit</th>
                <th class="text-center">Debit</th>
                <th class="text-center">Credit</th -->
                <th class="text-center">Debit</th>
                <th class="text-center">Credit</th>
              </tr>
            </thead>
            <tbody>
             <?php
              $total_op_debit = 0; $total_op_credit = 0;
              $total_bal_debit = 0; $total_bal_credit = 0;
              $total_tr_debit = 0;
              $total_tr_credit = 0;
              $acc_origin = '';
             ?>
              @foreach($rows as $row)
              <?php
                $op  = $row->op_debit - $row->op_credit;
                $bal = $op + $row->tr_debit -  $row->tr_credit;

                if ( $op > 0 ) $total_op_debit = $total_op_debit + $op;
                if ( $op < 0 ) $total_op_credit = $total_op_credit + $op;

                //$total_bal  = $total_bal + $bal;

                if ( $bal > 0 ) $total_bal_debit = $total_bal_debit + $bal;
                if ( $bal < 0 ) $total_bal_credit = $total_bal_credit + $bal;

                $total_tr_debit = $total_tr_debit + $row->tr_debit;
                $total_tr_credit = $total_tr_credit + $row->tr_credit;

               
                ?>

              <!-- @if($acc_origin == '' || $acc_origin != explode('>>', $row->acc_origin)[0] )
              <tr>
                 <td width="19%" style="font-weight:bold">{{ explode('>>', $row->acc_origin)[0] }}</td>
                 <td width="13%" align="right"></td>
                 <td width="13%" align="right"></td>
              </tr>
              @endif -->

              @if($acc_origin == '' || $acc_origin2 != $row->acc_origin )
              <tr>
                 <td width="19%" style="font-weight:bold">{{ $row->acc_origin }} </td>
                 <td width="13%" align="right"></td>
                 <td width="13%" align="right"></td>
              </tr>
              @endif

              <tr>
                 <td width="19%">{{ $row->acc_head }}</td>
                 <!--td width="13%" align="right">{{ $op > 0 ? number_format($op,2):''}}</td>
                 <td width="13%" align="right">{{ $op < 0 ? number_format(abs($op),2):''}}</td>
                 <td width="13%" align="right">{{ number_format($row->tr_debit,2)=='0.00'?'':number_format($row->tr_debit,2) }}</td>
                 <td width="13%" align="right">{{ number_format($row->tr_credit,2)=='0.00'?'':number_format(abs($row->tr_credit),2) }}</td -->
                 <td width="13%" align="right">{{ $bal > 0 ? number_format($bal,2):''}}</td>
                 <td width="13%" align="right">{{ $bal < 0 ? number_format(abs($bal),2) :''}}</td>
              </tr>

              <?php 
                $acc_origin = explode('>>', $row->acc_origin)[0];
                $acc_origin2 = $row->acc_origin;
              ?>
              @endforeach
              <tr>
                <td align="right"><b>Total:</b></td>
                <!-- td align="right"><b>{{ number_format($total_op_debit,2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($total_op_credit),2) }}</b></td>
                <td align="right"><b>{{ number_format($total_tr_debit,2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($total_tr_credit),2) }}</b></td -->
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
<script type="text/javascript">
  var form = document.getElementById('myform');
  document.getElementById('btn2').onclick = function() {
      form.target = '_blank';
      form.submit();
  }
</script>
@stop
