<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 13px; font-family: "Arrial Narrow";}

  @media print {
      *{
          font-family: "Times New Roman" !important;
      }
      .header, .header-space,
      .footer, .footer-space {
      }
      .wrapper{
          margin-top: 50px;
      }
      .header {
          position: fixed;
          top: 14px;
      }
      .footer {
          position: fixed;

          bottom: 7px;
      }
      .footer p{
          margin: 0px 0px !important;
      }
      p{
          margin: 1px 0px;
          font-size: 15px;
          font-weight: 700;
          font-family: Khaled;
      }
      .wrapper p{

          font-size: 18px;
          font-weight: 700;
          font-family: Khaled;
      }
      .single p{
          font-size: 15px !important;
          margin: 0px 0px !important;
      }
      .single {
          min-height:20px;
          overflow: hidden;
      }
      .row{
          overflow: hidden;
      }
      .margin-t{
          height: 273px;
          width: 100%;
      }
  }

  table {

  }

  td {
    border-top: none;
    border: 1px solid black;
  }
  th {
      border: none;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }
  </style>
    <title>Item Ledger Report</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">
    <div class="col-md-12">
      <table>
        <thead>
          <thead>
              <tr><th align="center" width="100%" colspan="13"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="13"><font size="2"><b>Stock Ledger</b><font></th></tr>
              <tr><th align="left" colspan="2"><b>{{$item->item_name}}({{$item->itm_cat_origin}}{{$item->itm_cat_name}})</b></th>
                <th   align="right" colspan="6"><font size="3"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>

            <tr style="border:1px solid black">
                <th style="border:1px solid black" class="text-center">Date</th>
                <th style="border:1px solid black" class="text-center">Transaction No</th>
                <th style="border:1px solid black" class="text-center">Opening</th>
                <th style="border:1px solid black" class="text-center">Production</th>
                <th style="border:1px solid black" class="text-center">Delivery</th>
                <th style="border:1px solid black" class="text-center">Balance</th>  
                <th style="border:1px solid black" class="text-center">Remarks</th>
              </tr>
        </thead>
        <tbody>
              <?php $OPP = 0; $balance = 0;
              $T_SA=0; $T_RT=0; $T_ST=0; $T_SR=0; $T_SH=0; $T_EX=0; $T_DA=0;
              $total_production = 0;
              $total_delivery = 0;
              $i = 1;
              ?>
              @foreach($opening as $op)
                <?php $OPP = $op->OP + $op->GR + $op->SA + $op->RT + $op->GI + $op->CI + $op->FR +
                  $op->SH + $op->EX + $op->DA;  ?>
              @endforeach

              @foreach($transactions as $row)
              <?php $OP = $OPP + $row->OP +  $balance;
              $total = $OP + $row->GR;
              $T_SA =  $T_SA + $row->SA;
              $T_RT =  $T_RT + $row->RT; 
              $T_ST =  $T_ST + $row->GI + $row->CI;
              $T_SR =  $T_SR + $row->FR;
              $T_SH =  $T_SH + $row->SH;
              $T_EX =  $T_EX + $row->EX;
              $T_DA =  $T_DA + $row->DA;

              ?>
              <tr>
                <td align="center"  width="12%">{{ date('d-m-Y',strtotime($row->item_op_dt)) }}</td>
                <td align="center"  width="15%">{{ $row->item_trans_desc=='EX'?'PL':$row->item_trans_desc }}-{{ $row->item_trans_ref_no }}</td>
                <td align="center"  width="10%">{{ $OP }}</td>
                <td align="center"  width="15%">{{ abs($row->DA) }}</td>
                <td align="center"  width="10%">{{ abs($row->SA) }}</td>
                
                <?php $balance = $OP + $row->GR + $row->SA + $row->RT + $row->GI + $row->CI + $row->FR + $row->SH + $row->EX + $row->DA;
                $OPP = 0;
                $total_production += abs($row->DA);
                $total_delivery += abs($row->SA);
                ?>
                <td align="center"  width="10%">{{ abs($balance) }}</td> 
                <td  width="25%">{{ $row->cust_name }}</td>
              </tr>
              <?php $i++ ?>
              @endforeach
              <tr>
                <td align="center" colspan="3"><b>Total</b></td>
                <td align="center"><b>{{ $total_production }}</b></td>
                <td align="center"><b>{{ $total_delivery }}</b></td>
                <td align="center"><b></b></td>
                <td align="center"><b></b></td>
              </tr>

              <tr>
                <td align="center" colspan="3"><b>Per Day Production</b></td>
                <td align="center"><b>{{ $total_production / $i }}</b></td>
                <td align="center"><b></b></td>
                <td align="center"><b></b></td>
                <td align="center"><b></b></td>
              </tr>
            </tbody>
        </table>
    </div>
  </div>

</div>
</section>
</body>
</html>
