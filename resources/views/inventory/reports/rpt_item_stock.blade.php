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
        <font size="3" color="blue"><b>Item Stock Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('itm.stock.rpt')}}" id="acc_form" method="post">
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
        <div class="col-md-2">
           <div class="input-group-prepend">
             <select name="itm_warehouse" class="chosen-select" id="itm_warehouse" required>
                 @if ($warehouse_list->count())
                     @foreach($warehouse_list as $list)
                         <option {{ $itm_warehouse == $list->w_ref_id ? 'selected' : '' }} value="{{$list->w_ref_id}}" >{{ $list->ware_name }}</option>
                     @endforeach
                 @endif
             </select>
            </div>
       </div>
        <div class="col-md-3">
           <div class="input-group  mb-2">
               <div class="input-group ss-item-required">
                   <select id="item_id" name="item_id" class="chosen-select" >
                     <option value="" disabled selected>- Select Item -</option>
                       @foreach($item_list as $item)
                         <option {{ $item_id == $item->itm_cat_code ? 'selected' : '' }} value="{{ $item->itm_cat_code }}">{{ $item->itm_cat_name }}</option>
                       @endforeach
                   </select>
                   @error('item_id')
                   <span class="text-danger">{{ $message }}</span>
                   @enderror
               </div>
             </div>
        </div>
          <div class="col-md-3">
            <div class="form-group">
              <input type="text" size = "8" name="fromdate" onclick="displayDatePicker('fromdate');"  value="{{ date('d-m-Y',strtotime($fromdate)) }}" />
              <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
              &nbsp;&nbsp;&nbsp;
              <input type="text" size = "8" name="todate" onclick="displayDatePicker('todate');"  value="{{ date('d-m-Y',strtotime($todate)) }}" />
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
          <table class="table table-striped table-data table-view">
            <thead class="thead-light">
              <tr>  
                <th class="text-center">Item Category</th>
                <th class="text-center">Item Code</th>
                <th class="text-center">Item Name</th>
                <th class="text-center">Opening</th>
                <th class="text-center">Purchase</th> 
                <th class="text-center">Sales</th>
                <th class="text-center">Return</th>
                <th class="text-center">Issue</th>
                <th class="text-center">Received</th>
                <th class="text-center">Process Loss</th>
                <th class="text-center">Shortage</th>
                <th class="text-center">Damage</th>
                <th class="text-center">Balance</th> 
              </tr>
            </thead>
            

            

            <tbody>
              <?php
             
              $itm_code = '';
              $op_gr_total = 0; 
              $gr_gr_total = 0;
              $sa_gr_total = 0; 
              $rt_gr_total = 0;  
              $st_gr_total = 0; 
              $sr_gr_total = 0;
              $sh_gr_total = 0;  
              $ex_gr_total = 0; 
              $da_gr_total = 0;
              $gr_value_total = 0;

              $op_total = 0; $gr_total = 0;
              $sa_total = 0; $rt_total = 0; 
              $st_total = 0; $sr_total = 0;
              $ex_total = 0; $sh_total = 0; $da_total = 0;
              $value_total = 0;
              ?>
            @foreach($rows as $row)
              <?php
                $op_gr_total += $row->OP;
                $gr_gr_total += $row->GR;
                $sa_gr_total += $row->SA;
                $rt_gr_total += $row->RT;
                $st_gr_total += $row->GI + $row->CI;
                $sr_gr_total += $row->FR; 
                $ex_gr_total += $row->EX;
                $sh_gr_total += $row->SH;
                $da_gr_total += $row->DA;

                $gr_value_total += ($row->OP+$row->GR+$row->SA+$row->RT+$row->GI+$row->CI+$row->FR+
                $row->EX+$row->SH+$row->DA);
              ?>
              @if($itm_code != '' && $itm_code != $row->itm_cat_name)
                <tr>
                  <td align="right" colspan="3"><b>Sub Total :</b></td>
                  <td align="right"><b>{{ number_format(abs($op_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($gr_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($sa_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($rt_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($st_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($sr_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($ex_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($sh_total),2) }}</b></td> 
                  <td align="right"><b>{{ number_format(abs($da_total),2) }}</b></td>
                  <td align="right"><b>{{ number_format(abs($op_total+$gr_total+$sa_total
                  +$rt_total+$st_total+$sr_total +$sh_total+$ex_total+$da_total),2) }}</b></td> 
                </tr>
                <?php $op_total = 0;
                $op_total = 0; $gr_total = 0;
                $sa_total = 0; $rt_total = 0; $sh_total = 0;
                $st_total = 0; $sr_total = 0;
                $ex_total = 0; $da_total = 0; $value_total = 0; ?>
              @endif

                @if($itm_code == '' || $itm_code != $row->itm_cat_name)
                  <tr>
                    <td colspan="6"><b>{{ $row->itm_cat_name }}({{ $row->itm_cat_origin }} )</b></td> 
                    <td></td><td></td>
                    <td></td><td></td>
                    <td></td><td></td> 
                  </tr>
                @endif
                <?php $bal = $row->OP + $row->GR + $row->SA + $row->RT + $row->GI + $row->CI + $row->FR + $row->SH + $row->EX + $row->DA;
                  
                  // GR = Raw Mat.Received
                  // GI = Issue To Prod.
                  // CI = Consumable Items Issue
                  // FR = Finish Goods Received
                  // SA = Sales
                  ?>
                <tr>
                  <td></td>
                  <td><b>{{ $row->item_code }}</b></td>
                  <td><b>{{ $row->item_name }}</b></td>
                  <td align="right">{{ number_format(abs($row->OP),2) }}</td>
                  <td align="right">{{ number_format(abs($row->GR),2) }}</td>
                  <td align="right">{{ number_format(abs($row->SA),2) }}</td>
                  <td align="right">{{ number_format(abs($row->RT),2) }}</td>
                  <td align="right">{{ number_format(abs($row->GI + $row->CI),2) }}</td>
                  <td align="right">{{ number_format(abs($row->FR),2) }}</td>
                  <td align="right">{{ number_format(abs($row->EX),2) }}</td>
                  <td align="right">{{ number_format(abs($row->SH),2) }}</td> 
                  <td align="right">{{ number_format(abs($row->DA),2) }}</td>
                  <td align="right">{{ number_format(abs($bal),2) }}</td> 
                </tr>
                <?php $itm_code = $row->itm_cat_name;
                $op_total += $row->OP;
                $gr_total += $row->GR;
                $sa_total += $row->SA;
                $rt_total += $row->RT;
                $st_total += $row->GI + $row->CI;
                $sr_total += $row->FR;
                $sh_total += $row->SH;
                $ex_total += $row->EX;
                $da_total += $row->DA;
                $value_total += 1;
                ?>
            @endforeach

              <tr>
                <td align="right" colspan="3"><b>Sub Total :</b></td>
                <td align="right"><b>{{ number_format(abs($op_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($sa_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($rt_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($st_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($sr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($ex_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($sh_total),2) }}</b></td> 
                <td align="right"><b>{{ number_format(abs($da_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($op_total+$gr_total+$sa_total
                  +$rt_total+$st_total+$sr_total+$sh_total+$ex_total+$da_total),2) }}</b></td> 
              </tr>

              <tr>
                <td colspan="3"><b>Grand Total</b></td>
                <td align="right"><b>{{ number_format(abs($op_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($gr_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($sa_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($rt_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($st_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($sr_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($ex_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($sh_gr_total),2) }}</b></td> 
                <td align="right"><b>{{ number_format(abs($da_gr_total),2) }}</b></td>
                <td align="right"><b>{{ number_format(abs($op_gr_total + $gr_gr_total +
                  $sa_gr_total + $rt_gr_total + $st_gr_total + $sr_gr_total + $sh_gr_total +
                  $ex_gr_total + $da_gr_total),2) }}</b></td>  
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
