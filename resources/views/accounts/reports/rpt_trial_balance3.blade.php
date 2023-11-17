@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')

<?php 
$totalCashAtBank = 0;
foreach($cashAtBank as $cashat){
    $totalCashAtBankamount = $cashat->tr_debit - $cashat->tr_credit; 
    $totalCashAtBank += $totalCashAtBankamount;
}

$totalcashInHand = 0;
foreach($cashInHand as $cashatHand){
    $cashInHandamount = $cashatHand->tr_debit - $cashatHand->tr_credit; 
    $totalcashInHand += $cashInHandamount;
}

?>


<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller ">
        <font size="3" color="blue"><b>Trading, Profit And Loss Account, Balance Sheet</b></font>
       
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.trial.bal3')}}" id="acc_form" method="post">
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
      <?php  
        $in = 0;
        $parent = array(1); 
        $pur = 0;
      ?>
      @foreach($rows as $row)     
        @if($row->acc_code == 8 or $row->acc_code == 101)
          
          <?php 
            $in += $row->tr_debit -  $row->tr_credit;
          ?>
        @endif

        @if($row->acc_code == 7)
          
          <?php 
            $pur += $row->tr_debit -  $row->tr_credit;
          ?>
        @endif
      @endforeach

      </form>

      

      <div class="row justify-content-center">
        <h4 class="text-center font-weight-bold">Manufacturing, Trading, Profit And Loss Account</h4>
        
      </div>
      <div class="row justify-content-center font-weight-bold">
          FROM {{ $fromdate }} TO {{ $todate }}
      </div>
      <br>
      <div class="row justify-content-center">
        
        <div class="col-md-6" style="padding-right:0px !important">
          <!-- <h4 class="text-center"> EXPENSE </h4> -->
          <table class="table table-striped table-data table-report">
            <thead class="thead-light">
              <tr>
                <th width="19%">Particulars</th>
                <th width="13%" align="center"> Amount</th>
              </tr>
            </thead>
            <tbody>

              <!-- <tr>
                <td width="19%"> OPENING STOCK </td>
                <td width="13%" align="right"> {{ $ops }}</td>
              </tr> -->

              <tr>
                <td width="19%" colspan="2"> <b>OPENING STOCK :</b> </td>
              </tr>
                <?php 
                    $openingStock = 0;
                ?>
                <!-- stock value -->
                @foreach($stocks as $stock)
                    <?php  $opamount =  $stock->op_debit - $stock->op_credit ; 
                        $openingStock += $opamount;
                    ?>
                    @if($opamount != 0)
                        <tr>
                            <td width="19%"> {{$stock->p_acc_head}} -- {{ $stock->acc_head }}</td>
                            <td width="19%" align="left"> {{ $opamount }} </td>
                        </tr>
                    @endif
                @endforeach

              <tr>
                <td width="19%" align="right"><b> Total</b></td>
                <td width="13%" align="right"><b> {{ $openingStock }}</b></td>
              </tr>

              <tr>
                <td width="19%" colspan="2"> <b>PURCHASE :</b> </td>
              </tr>
                
              <?php 
                $totalPurchaseamount = 0;              
                ?>
                <!-- stock value -->
                @foreach($stocks as $stock)
                    <?php  $purchaseamount =  $stock->tr_debit ; 
                            $totalPurchaseamount += $purchaseamount;
                    ?>
                    @if($purchaseamount != 0)
                        <tr>
                            <td width="19%"> {{$stock->p_acc_head}} -- {{ $stock->acc_head }}</td>
                            <td width="19%" align="left"> {{ $purchaseamount }} </td>
                        </tr>
                    @endif
                @endforeach

              <tr>
                <td width="19%" align="right"><b> TOTAL</b></td>
                <td width="13%" align="right"><b> {{ $totalPurchaseamount }}</b></td>
              </tr>
           
              <tr>
                <td width="19%" colspan="2"> <b>Expenses :</b> </td>
              </tr>
                
              <?php 
                $totalPurchase = 0;              
                ?>
                <!-- stock value -->
                @foreach($expenses as $expense)
                    <?php  $expenseamount =  $expense->tr_debit - $expense->tr_credit ; 
                            $totalexpense += $expenseamount;
                    ?>
                    @if($expenseamount != 0)
                        <tr>
                            <td width="19%"> {{$expense->p_acc_head}} -- {{ $expense->acc_head }}</td>
                            <td width="19%" align="left"> {{ $expenseamount }} </td>
                        </tr>
                    @endif
                @endforeach

              <tr>
                <td width="19%" align="right"><b> TOTAL</b></td>
                <td width="13%" align="right"><b> {{ $totalexpense }}</b></td>
              </tr>
              
              </tbody>
          </table>
        </div>

        <div class="col-md-6" style="padding-left:0px !important">
          <!-- <h4 class="text-center"> INCOME </h4> -->
          <table class="table table-striped table-data table-report">
            <thead class="thead-light">
              <tr>
                <th width="19%">Particulars</th>
                <th width="13%" align="center"> Amount </th>
              </tr>
             
            </thead>
            <tbody>

            <tr>
                <td width="19%" colspan="2"> <b>Sales :</b> </td>
            </tr>
            <?php 
                $totalSales = 0;
            ?>
            <!-- incomes value -->
            @foreach($incomes as $income)
                <?php  
                    $saleamount = $income->tr_credit - $income->tr_debit; 
                    $totalSales += $saleamount;
                ?>
                @if($saleamount != 0)
                <tr>
                    <td width="19%"> {{$income->acc_head}} </td>
                    <td width="19%"> {{ $saleamount }} </td>
                </tr>
                @endif
            @endforeach

            <tr>
                <td width="19%"> <b>Total :</b> </td>
                <td width="19%" align="right"> <b>{{ $totalSales }}</b> </td>
            </tr>

            <tr>
                <td width="19%" colspan="2"> <b>Closing Stock :</b> </td>
            </tr>

            <?php 
                $totalLossStock = 0;
            ?>
            <!-- stock value -->
            @foreach($stocks as $stock)
                <?php  
                    $opening =  $stock->op_debit - $stock->op_credit ; 
                    $purchase =  $stock->tr_debit - $stock->tr_credit ; 
                    $lossamount = $opening + $purchase;
                    $totalLossStock += $lossamount;
                ?>
                @if($lossamount != 0)
                    <tr>
                        <td width="19%"> {{$stock->p_acc_head}} -- {{ $stock->acc_head }}</td>
                        <td width="19%"> {{ $lossamount }} </td>
                    </tr>
                @endif
            @endforeach

              <tr>
                <td width="19%" align="left"><b> </b></td>
                <td width="13%" align="right"><b> <?php echo abs($totalLossStock); ?></b></td>
              </tr>

              </tbody>
          </table>
        </div>
      </div>


       <!--  grand total --> 
       <div class="row justify-content-center">
         <div class="col-md-12">
            <table class="table table-striped table-data table-report">
              <tbody>
                <tr >
                    <td width="25%" align="right"><b>Net Profit/Loss: Col Total: {{ $totalexpense + $openingStock + $totalPurchaseamount }}</b></td>
                    <td width="25%" align="right"><b> {{  abs($totalLossStock +  $totalSales) - ($totalexpense + $openingStock + $totalPurchaseamount)  }}</b></td>
                    <td width="25%" align="right"><b> </b></td>
                    <td width="25%" align="right"> <b> </b></td>
                 
                </tr>

                <!-- 2nd row  -->
                <tr style="border: 2px solid black">
                    <td width="25%" align="right"><b> </b></td>
                    <td width="25%" align="right"><b> {{ $totalLossStock +  $totalSales}}</b></td>
                    <td width="25%" align="right"><b> </b></td>
                    <td width="25%" align="right"> <b>{{ $totalLossStock +  $totalSales}}</b></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>


      <br>
      <br>

      <div class="row justify-content-center" >
        <h4 class="text-center font-weight-bold" style="font-size: 16px">Balance Sheet</h4>
      </div>

      <div class="row justify-content-center font-weight-bold" >
          As On  {{ $todate }}
      </div>

      <div class="row justify-content-center">
        
        <div class="col-md-6" style="padding-right:0px !important">
          <h4 class="text-center"> ASSETS </h4>
          <table class="table table-striped table-data table-report">
            <thead class="thead-light">
              <tr>
                <th width="19%">Particulars</th>
                <th width="13%" align="center"> Amount </th>
              </tr>

            </thead>
            <tbody>
              <tr>
                <td width="19%"> CASH AT BANK</td>
                <td width="13%" align="right"> {{  $totalCashAtBank }}</td>
              </tr>

              <tr>
                <td width="19%"> CASH IN HAND</td>
                <td width="13%" align="right"> {{  $totalcashInHand }}</td>
              </tr>

              <?php   
                  $totalassets = 0;
              ?>
              @foreach($assets as $asset)
                <?php   
                  $totalassets += $asset->balance;
                ?>
                <tr>
                    <td width="19%"> {{ $asset->acc_origin }} </td>
                    <td width="19%" align="right"> {{ $asset->balance }} </td>
                </tr>
                 
              @endforeach

              <!-- <tr>
                <td width="19%" align="right"> <b>TOTAL</b></td>
                <td width="13%" align="right"><b> {{  abs($totalassets) + $totalCashAtBank + $totalcashInHand }}</b></td> 
              </tr> -->
      
              <tr>
                <td width="19%" align="left"> CLOSING STOCK</td>
                <td width="13%" align="right"> {{  abs($totalLossStock) }}</td> 
              </tr>

              
              </tbody>
          </table>
        </div>

        <div class="col-md-6" style="padding-left:0px !important">
          <h4 class="text-center"> LIABILITIES </h4>
          <table class="table table-striped table-data table-report">
            <thead class="thead-light">
              <tr>
                <th width="19%">Particulars</th>
                <th width="13%" align="center"> Amount </th>
              </tr>
        
            </thead>
            <tbody>
           
              <?php   
                  $totalLiabilities = 0;
              ?>
              @foreach($liabilities as $key => $lia)
                <?php   
                  $totalLiabilities += abs($lia->balance);
                ?>
                <tr>
                    <td width="19%"> {{ $lia->acc_origin }} </td>
                    <td width="19%" align="right"> {{ abs($lia->balance) }} </td>
                </tr>
              @endforeach

                <tr>
                  <td width="50%" align="left"><b>Net Profit/Loss: Col Total: {{ $totalLiabilities }} </b></td>
                  <th width="50%" align="right"> {{  abs($totalLossStock +  $totalSales) - ($totalexpense + $openingStock + $totalPurchaseamount)  }} </th>
                </tr>
        
              </tbody>
          </table>
        </div>
      </div>

      <!--  grand total -->
      <div class="row justify-content-center">
         <div class="col-md-12">
            <table class="table table-striped table-data table-report">
              <tbody>
                <tr>
                  <td width="50%" align="right"><b>  {{  abs($totalassets) + $totalCashAtBank + $totalcashInHand + abs($totalLossStock)  }}</b></td>
                  <td width="50%" align="right"><b>  {{ $totalLiabilities - ($totalLossStock +  $totalSales) - ($totalexpense + $openingStock + $totalPurchaseamount) }} </b></td>
                 
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
