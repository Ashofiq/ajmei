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
              <tr><th align="center" width="100%" colspan="8"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="8"><font size="2"><b>Stock Ledger</b><font></th></tr>
              <tr><th align="left" colspan="4"><b>{{$item->item_name}}({{$item->item_desc}})</b></th><th align="right" colspan="4"><font size="3"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>

          <tr>
            <td align="center" width="3%"><b>Date</b></td>
            <td align="center" width="19%"><b>Opening</b></td>
            <td align="center" width="13%"><b>Purchase</b></td>
            <td align="center" width="13%"><b>Total</b></td>
            <td align="center" width="13%"><b>Sales</b></td>
            <td align="center" width="13%"><b>Shortage</b></td>
            <td align="center" width="13%"><b>Date Exp.</b></td>
            <td align="center" width="13%"><b>Damage</b></td>
            <td align="center" width="13%"><b>Balance</b></td>
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
</body>
</html>
