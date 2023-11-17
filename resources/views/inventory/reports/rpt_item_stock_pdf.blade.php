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
              <tr><th align="center" colspan="13"><font size="2"><b>Item Stock Report</b><font></th></tr>
              <tr><th align="right" colspan="13"><font size="3"><b>Date Range:&nbsp;{{ date('d-m-Y', strtotime($fromdate)) }} to {{ date('d-m-Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>
          <tr>
            <td align="left" colspan="13"><b>&nbsp;</b></td>
          </tr>
          <tr>
            <td align="center" width="10%"><b>Item<br/>Category</b></td>
            <td align="center" width="8%"><b>Item<br/>Code</b></td>
            <td align="center" width="15%"><b>Item Name</b></td>
            <td align="center" width="8%"><b>Opening</b></td>
            <td align="center" width="8%"><b>Purchase</b></td>
            <td align="center" width="8%"><b>Sales</b></td>
            <td align="center" width="8%"><b>Return</b></td>
            <td align="center" width="8%"><b>Issue</b></td>
            <td align="center" width="8%"><b>Received</b></td>
            <td align="center" width="8%"><b>Process Loss</b></td>
            <td align="center" width="8%"><b>Shortage</b></td> 
            <td align="center" width="8%"><b>Damage</b></td>
            <td align="center" width="10%"><b>Balance</b></td> 
          </tr>
        </thead>
        <tbody>
              <?php $itm_code = '';
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
                    <td colspan="8"><b>{{ $row->itm_cat_name }}({{ $row->itm_cat_origin }} )</b></td> 
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
</body>
</html>
