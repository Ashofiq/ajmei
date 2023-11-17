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
        <font size="3" color="blue"><b>Daily Cash Statement</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.cash.sheet')}}" id="acc_form" method="post">
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
        <!-- div class="col-md-1">
           <div class="input-group  mb-2">
             <div class="input-group ss-item-required">
                <select id="ledger_id" name="ledger_id" class="chosen-select" >
                   <option value="" disabled selected>- Select Ledger -</option>
                     @foreach($ledgers as $ledger)
                       <option {{ old('ledger_id') == $ledger->id ? 'selected' : '' }} value="{{ $ledger->id }}">{{ $ledger->acc_head }}</option>
                     @endforeach
                 </select>
                 @error('inv_no')
                 <span class="text-danger">{{ $message }}</span>
                 @enderror>
             </div>
             </div>
        </div -->
        <input type="hidden" name="ledger_id" id = "ledger_id" value="{{$ledger->id}}" />

        <div class="col-md-2">
            <div class="form-group">
              <input type="text" size = "15" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
              <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
           </div>
        </div>
        <div class="col-md-2">
          <button type="submit" name="submit" id='btn1' value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
          &nbsp;<button type="submit" name="submit" id='btn2' value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-view">
            <thead class="thead-light">
              <tr>
                <th class="text-center">SL</th>
                <th class="text-center">Head of Accounts</th>
                <th class="text-center">Particulars</th>
                <th class="text-center">Received</th>
                <th class="text-center">Payment</th>
                <th class="text-center">Balance</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                @foreach($openings as $opening)
                <?php
                $opening1 = $opening->debit - $opening->credit;
                $opening = $opening->debit - $opening->credit;
                $total_Debit = 0;   $total_Credit = 0;?>
                @endforeach
                <td colspan="5">Opening Balance :</td>
                <td width="10%" align="right">{{ number_format($opening,2) }}</td>
              </tr>
              <?php  $i=0; ?>

              @foreach($rows_bank_rec as $row)
              <?php $balance = $opening + $row->c_amount - $row->d_amount;
              $total_Debit = $total_Debit + $row->d_amount;
              $total_Credit = $total_Credit + $row->c_amount; ?>
              <tr>
                <td>{{ $i +=1 }}</td>
                <td>&nbsp;{{ $row->acc_head }}</td>
                <td>{{ $row->t_narration }}</td>
                <td align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
                <td align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
                <td align="right">{{ number_format($balance,2) }}</td>
              </tr>
              <?php $opening = $balance; ?>
              @endforeach

              @foreach($rows_cash_rec as $row)
              <?php
              $balance = $opening + $row->c_amount - $row->d_amount;
              $total_Debit = $total_Debit + $row->d_amount;
              $total_Credit = $total_Credit + $row->c_amount; ?>
              <tr>
                <td width="3%">{{ $i +=1 }} </td>
                <td width="25%">{{ $row->acc_head }}</td>
                <td width="42%">{{ $row->t_narration }}</td>
                <td width="10%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
                <td width="10%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
                <td width="10%" align="right">{{ number_format($balance,2) }}</td>
              </tr>
              <?php $opening = $balance; ?>
              @endforeach

              @foreach($rows_payment as $row)
              <?php
              $balance = $opening + $row->c_amount - $row->d_amount;
              $total_Debit = $total_Debit + $row->d_amount;
              $total_Credit = $total_Credit + $row->c_amount; ?>
              <tr>
                <td width="3%">{{ $i +=1 }} </td>
                <td width="25%">{{ $row->acc_head }}</td>
                <td width="42%">{{ $row->t_narration }}</td>
                <td width="10%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
                <td width="10%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
                <td width="10%" align="right">{{ number_format($balance,2) }}</td>
              </tr>
              <?php $opening = $balance; ?>
              @endforeach

              <tr>
                <td colspan="3" align="right"><b>Total:</b></td>
                <td width="8%" align="right"><b>{{ number_format($total_Credit,2) }}</b></td>
                <td width="8%" align="right"><b>{{ number_format($total_Debit,2) }}</b></td>
                <td width="8%" align="right"><b></b></td>
              </tr>
              </tbody>
            </table>
            </div>
      </div>
      <br/>
      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-view">
            <thead class="thead-light">
              <tr>
                <th class="text-center">SL</th>
                <th class="text-center">Head of Accounts</th>
                <th class="text-center"></th>
                <th class="text-center">&nbsp;</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=0; $Total_AMT=0; ?>
              @foreach($data as $d)
              <?php
                $amount = $d->d_amount -  $d->c_amount;
                $Total_AMT += $amount;
              ?>
                @if($amount != '0')
              <tr>
                <td width="3%">{{ $i +=1 }} </td>
                <td width="25%">{{ $d->acc_head }}</td>
                <td width="42%">{{ $d->t_narration }}</td>
                <td width="10%" align="right">&nbsp;</td>
                <td width="10%" align="right">{{ number_format($amount,2)=='0.00'?'0.00':number_format($amount,2) }}</td>
                <td width="10%" align="right">&nbsp;</td>
              </tr>
              @endif
              @endforeach
                <tr>
                    <td colspan="4" align="right"><b>Total Advance:</b></td>
                    <td align="right"><b>{{ number_format($Total_AMT,2) }}</b></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" align="right"><b>Cash in Hand:</b></td>
                    <!-- td align="right"><b>{{ number_format($opening + $Total_AMT,2) }}</b></td -->
                    <td align="right"><b>{{ number_format($CashinHand->CashinHand,2) }}</b></td>

                    <td align="right">&nbsp;</td>
                </tr>
              </tbody>
            </table>
            </div>
      </div>
      <br/>
      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-view">
            <tbody>
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="67%">Opening Balance</td>
                <td width="10%">&nbsp;</td>
                <td width="10%" align="right">{{ number_format($opening1,2) }}</td>
                <td width="10%">&nbsp;</td>
              </tr>

              <tr>
                <td width="3%">&nbsp;</td>
                <td width="67%">Total Received</td>
                <td width="10%">&nbsp;</td>
                <td width="10%" align="right">{{ number_format($total_Credit,2) }}</td>
                <td width="10%">&nbsp;</td>
              </tr>
              <?php $subtotal = $opening1 + $total_Credit; ?>
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="67%">Sub-Total</td>
                <td width="10%">&nbsp;</td>
                <td width="10%" align="right">{{ number_format($subtotal,2) }}</td>
                <td width="10%">&nbsp;</td>
              </tr>

              <tr>
                <td width="3%">&nbsp;</td>
                <td width="67%">Expenses</td>
                <td width="10%">&nbsp;</td>
                <td width="10%" align="right">{{ number_format($total_Debit,2) }}</td>
                <td width="10%">&nbsp;</td>
              </tr>

              <?php $balance = $subtotal - $total_Debit; ?>
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="67%">Balance as per cash book</td>
                <td width="10%">&nbsp;</td>
                <td width="10%" align="right">{{ number_format($balance,2) }}</td>
                <td width="10%">&nbsp;</td>
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
  <script>
    var form = document.getElementById('myform');
    document.getElementById('btn2').onclick = function() {
      form.target = '_blank';
      form.submit();
  }
    function autocompleteAccHead(){
      compcode = $('#company_code').val()
        $('#acc_Ledger').autocomplete({

          source: function(req, res){
            $.ajax({
              url: "/report/get-ledger-head",
              dataType: "json",
              data:{'item':encodeURIComponent(req.term),
                  'compcode':encodeURIComponent(compcode) },

              error: function (request, error) {
                   console.log(arguments);
                   alert(" Can't do because: " +  console.log(arguments));
              },

              success: function (data) {
                res($.map(data.data, function (item) {
                //alert('IQII:'+item.acc_head)
                return {
                    label: item.acc_head,
                    value: item.acc_head,
                    acc_id: item.id,
                  };
                }));
              }
            });
          },
          autoFocus:true,
          select: function(event, ui){
            //alert(ui.item.acc_id)
            $('#ledger_id').val(ui.item.acc_id)
          }
        })
    }

  </script>
@stop
