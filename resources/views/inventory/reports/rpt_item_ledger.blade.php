@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Item Ledger Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('itm.stock.ledger.rpt')}}" id="acc_form" method="post">
      {{ csrf_field() }}
      <div class="row justify-content-center">
        <div class="col-md-2">
           <div class="input-group mb-2">
             <select class="form-control m-bot15" id="company_code" name="company_code" required>
               <option value="" >--Select Company--</option>
                @if ($companies->count())
                    @foreach($companies as $company)
                        <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                    @endforeach
                @endif
            </select>
           </div>
        </div>
        <div class="col-md-4">
           <div class="input-group  mb-2">
               <div class="input-group ss-item-required">
                   <select id="item_id" name="item_id" class="chosen-select" >
                     <option value="" disabled selected>- Select Item -</option>
                       @foreach($item_list as $item)
                         <option {{ $item_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->item_name }}({{$item->itm_cat_origin}}{{$item->itm_cat_name}})</option>
                       @endforeach
                   </select>
                   @error('item_id')
                   <span class="text-danger">{{ $message }}</span>
                   @enderror
               </div>
             </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
              <input type="text" size = "15" name="fromdate" onclick="displayDatePicker('fromdate');"  value="{{ date('d-m-Y',strtotime($fromdate)) }}" />
              <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
           </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
              <input type="text" size = "15" name="todate" onclick="displayDatePicker('todate');"  value="{{ date('d-m-Y',strtotime($todate)) }}" />
              <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
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
          <table class="table table-striped table-data table-report">
            <thead class="thead-light">
              <tr>
                <th class="text-center">Date</th>
                <th class="text-center">Opening</th>
                <th class="text-center">Purchase</th>
                <th class="text-center">Total</th>
                <th class="text-center">Sales</th>
                <th class="text-center">Shortage</th>
                <th class="text-center">Date Exp.</th>
                <th class="text-center">Damage</th>
                <th class="text-center">Balance</th>
              </tr>
            </thead>
            <tbody>
              <?php $OPP = 0; $balance = 0; ?>
              @foreach($opening as $op)
                <?php $OPP = $op->OP + $op->GR + $op->SA +
                  $op->SH + $op->EX + $op->DA  ?>
              @endforeach

              @foreach($transactions as $row)
              <?php $OP = $row->OP + $OPP + $balance;
              $total = $OP + $row->GR;
              ?>
              <tr>
                <td align="center">{{ $row->item_op_dt }}</td>
                <td align="center">{{ $OP }}</td>
                <td align="center">{{ $row->GR }}</td>
                <td align="center">{{ $total }}</td>
                <td align="center">{{ abs($row->SA) }}</td>
                <td align="center">{{ abs($row->SH) }}</td>
                <td align="center">{{ abs($row->EX) }}</td>
                <td align="center">{{ abs($row->DA) }}</td>

                <?php $balance = $OP + $row->GR + $row->SA +
                  $row->SH + $row->EX + $row->DA;
                  $OPP = 0;
                ?>
                <td align="center">{{ abs($balance) }}</td>
              </tr>
              @endforeach

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
