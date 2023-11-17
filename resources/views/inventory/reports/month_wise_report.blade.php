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
        <font size="3" color="blue"><b>Month Wise Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('month.wise.report')}}" id="acc_form" method="post">
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
        <div class="col-md-4" style="display:none">
           <div class="input-group  mb-2">
               <div class="input-group ss-item-required">
                   <select id="item_id" name="item_id" class="chosen-select" >
                      <option value="557"></option>
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
                <th class="text-center" colspan="3">Starting Stock</th>
                <th class="text-center" colspan="3">Jute Purchase</th>
                <th class="text-center" colspan="3">Total Jute Purchase</th>
                <th class="text-center" colspan="3">Jute Issue</th>
                <th class="text-center" colspan="3">Finish Stock</th>

              </tr>
              <?php 
                $tosaOpening = (isset($transactions[0]->tosa_opening)) ? $transactions[0]->tosa_opening : 0;
                $kuttingOpening = (isset($transactions[0]->kutting_opening)) ? $transactions[0]->kutting_opening : 0;
              ?>

              <tr>
                <th class="text-center" colspan="12"></th>
                <th class="text-center" >Opening: </th>
                <th class="text-center" colspan="1">{{ $tosaOpening }}</th>
                <th class="text-center" colspan="1">{{ $kuttingOpening }}</th>
                <th class="text-center" colspan="1">{{ $tosaOpening + $kuttingOpening  }}</th>
              </tr>

              <tr>
                <th class="text-center" >Up to Date</th>
                <th class="text-center">Tosa</th>
                <th class="text-center">Cutting</th>
                <th class="text-center">Total</th>
                <th class="text-center">Tosa</th>
                <th class="text-center">Cutting</th>
                <th class="text-center">Total</th>
                <th class="text-center">Tosa</th>
                <th class="text-center">Cutting</th>
                <th class="text-center">Total</th>
                <th class="text-center">Tosa</th>
                <th class="text-center">Cutting</th>
                <th class="text-center">Total</th>
                <th class="text-center">Tosa</th>
                <th class="text-center">Cutting</th>
                <th class="text-center">Total</th>
                
              </tr>
            </thead>
            <tbody>
            
           
              <?php 
                $finisTosa = 0;
                $finishKutting = 0;
                $finishTotal = 0;
              ?>
              @foreach($transactions as $key =>  $row)
              <?php 
                $openingTotal =  $row->tosa_opening  + $row->kutting_opening;
                $jutePurchaseTotal = $row->tosa_purchase + $row->kutting_purchase;
                $issueTotal = $row->tosa_issue + $row->kutting_issue;

                $totaltosaPurchase = $row->tosa_opening + $row->tosa_purchase;
                $totalKuttingPurchase = $row->kutting_opening + $row->kutting_purchase;
                $totalPurchaseTotal = $openingTotal +  $jutePurchaseTotal;
                
              ?>
              <tr>
                <td align="center">{{ date('d-m-Y', strtotime($row->t_date)) }}</td>
                <td align="center">{{ ($key == 0) ? $finisTosa + $row->tosa_opening : $finisTosa   }} </td>
                <td align="center"> {{ ($key == 0) ? $finishKutting + $row->kutting_opening : $finishKutting  }}</td>
                <td align="center"> {{ ($key == 0) ? $finishTotal + $row->tosa_opening + $row->kutting_opening : $finishTotal  }} </td>
                <td align="center"> {{ $row->tosa_purchase }}</td>
                <td align="center"> {{ $row->kutting_purchase }}</td>
                <td align="center"> {{  $jutePurchaseTotal }} </td> <!-- jute purchase Total  -->
                <td align="center"> {{ $totaltosaPurchase }} </td>
                <td align="center"> {{ $totalKuttingPurchase }}</td>
                <td align="center"> {{ $totalPurchaseTotal }}</td><!-- purchase Total  -->
                <td align="center"> {{ $row->tosa_issue }} </td> 
                <td align="center"> {{ $row->kutting_issue }}</td>
                <td align="center"> {{ $issueTotal }} </td> <!-- issue Total  -->
                <td align="center"> {{ $totaltosaPurchase - $row->tosa_issue }} </td> 
                <td align="center"> {{ $totalKuttingPurchase - $row->kutting_issue  }} </td>
                <td align="center"> {{ $totalPurchaseTotal - $issueTotal }} </td>

               
              </tr> 

              <?php

                  $finisTosa = $totaltosaPurchase - $row->tosa_issue;
                  $finishKutting = $totalKuttingPurchase - $row->kutting_issue;
                  $finishTotal = $totalPurchaseTotal - $issueTotal;
                ?>
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
