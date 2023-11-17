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
        <font size="3" color="blue"><b>Item Opening Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('itm.opening.rpt')}}" id="acc_form" method="post">
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
               <select name="itm_warehouse" class="chosen-select" id="itm_warehouse">
               <option value="" >--Select Wearhouse--</option>
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
                     <option value="" selected>- Select Item -</option>
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
        <div class="col-md-1.5">
            <div class="form-group">
              <input type="text" size = "10" name="fromdate" onclick="displayDatePicker('fromdate');"  value="{{ date('d-m-Y',strtotime($fromdate)) }}" /> </a>
              <!-- input type="date" name="fromdate" id="fromdate" value="{{$fromdate}}" class="form-control" placeholder="dd/mm/YYYY" required/ -->
           </div>
        </div>
        <div class="col-md-1.5">
            <div class="form-group">
              &nbsp;<input type="text" size = "10" name="todate" onclick="displayDatePicker('todate');"  value="{{ date('d-m-Y',strtotime($todate)) }}" /></a>

              <!-- input type="date" name="todate" id="todate" value="{{ old('todate') == "" ? $todate : old('todate') }}" class="form-control" placeholder="To Date" required/ -->
           </div>
        </div>
        <div class="col-md-1.5">
          &nbsp;<button type="submit" name="submit" id='btn1' value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
          &nbsp;<button type="submit" name="submit" id='btn2' value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-8">
          <table class="table table-striped table-data table-view">
            <thead class="thead-light">
              <tr>
                <th class="text-center">Warehoue</th>
                <th class="text-center">Item Category</th>
                <th class="text-center">Item Code</th>
                <th class="text-center">Item Name</th>
                <th class="text-center">Opening</th>
                <th class="text-center">Price</th>
                <th class="text-center">Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $itm_code   = '';
                $t_opening  = 0;
                $lot_total  = 0;
                $sub_amount = 0;
                $amount = 0;?>
              @foreach($rows as $row)
              <?php
                $t_opening += $row->item_op_stock;
                $amount += $row->item_op_stock*$row->item_base_price;
              ?>
              @if($itm_code != '' && $itm_code != $row->item_code)
              <tr>
                <td align="right" colspan="4"><b>Sub Total :</b></td>
                <td align="right"><b>{{ number_format($lot_total,2) }}</b></td>
                <td align="right"><b>&nbsp;</b></td>
                <td align="right"><b>{{ number_format($sub_amount,2) }}</b></td>
              </tr>
              <?php $lot_total = 0; $sub_amount = 0; ?>
              @endif

              @if($itm_code == '' || $itm_code != $row->item_code)

              <tr>
                <td>{{ $row->ware_name }}</td>
                <td>{{ $row->itm_cat_name }}</td>
                <td>{{ $row->item_code }}</td>
                <td>{{ $row->item_name }}</td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              @endif
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $row->item_lot_no }}</td>
                <td align="right">{{ number_format($row->item_op_stock,2) }}</td>
                <td align="right">{{ number_format($row->item_base_price,2) }}</td>
                <td align="right">{{ number_format($row->item_op_stock*$row->item_base_price,2) }}</td>
              </tr>
              <?php $itm_code = $row->item_code;
              $lot_total += $row->item_op_stock;
              $sub_amount += $row->item_op_stock*$row->item_base_price;?>
              @endforeach
              <tr>
                <td align="right" colspan="4"><b>Sub Total :</b></td>
                <td align="right"><b>{{ number_format($lot_total,2) }}</b></td>
                <td align="right"><b>&nbsp;</b></td>
                <td align="right"><b>{{ number_format($sub_amount,2) }}</b></td>
              </tr>
              <tr>
                <td align="right" colspan="4"><b>Grand Total :</b></td>
                <td align="right"><b>{{ number_format($t_opening,2) }}</b></td>
                <td align="right"><b>&nbsp;</b></td>
                <td align="right"><b>{{ number_format($amount,2) }}</b></td>
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
  </script>
@stop
