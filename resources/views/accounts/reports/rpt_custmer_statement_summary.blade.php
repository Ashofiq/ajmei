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
        <font size="3" color="blue"><b>Customer Summary Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.cust.statement.summary')}}"  method="post">
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
        <div class="col-md-4">
           <div class="input-group mb-2">
               <div class="input-group ss-item-required">
                   <select id="ledger_id" name="ledger_id" class="chosen-select">
                     <option value="" disabled selected>- Select Customer -</option>
                       @foreach($customers as $cust)
                         <option {{ $ledger_id == $cust->cust_chartofacc_id ? 'selected' : '' }} value="{{ $cust->cust_chartofacc_id }}">{{ $cust->cust_name }}</option>
                       @endforeach
                   </select>
                   @error('ledger_id')
                   <span class="text-danger">{{ $message }}</span>
                   @enderror
               </div>
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
                <th class="text-center">Date</th>  
                <th class="text-center">Debit</th>
                <th class="text-center">Credit</th>
                <th class="text-center">Balance</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <?php $opening = $opening->debit - $opening->credit;
                $total_Debit = 0;   $total_Credit = 0;?>

                <td colspan="3">Opening Balance :</td>
                <td width="10%" align="right">{{ number_format($opening,2) }}</td>
              </tr>
              @foreach($rows as $row)
              <?php $balance = $opening + $row->d_amount - $row->c_amount;
              $total_Debit = $total_Debit + $row->d_amount;
              $total_Credit = $total_Credit + $row->c_amount; ?>
              <tr>
                <td width="5%">{{ date('d-m-Y', strtotime($row->voucher_date)) }} </td> 
                <td width="8%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'':number_format($row->d_amount,2) }}</td>
                <td width="8%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'':number_format($row->c_amount,2) }}</td>
                <td width="8%" align="right">{{ number_format($balance,2) }}</td>
              </tr>
              <?php $opening = $balance; ?>
              @endforeach
              <tr>
                <td colspan="1" align="right"><b>Total:</b></td>
                <td width="8%" align="right"><b>{{ number_format($total_Debit,2) }}</b></td>
                <td width="8%" align="right"><b>{{ number_format($total_Credit,2) }}</b></td>
                <td width="8%" align="right"><b></b></td>
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
  <script>
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
