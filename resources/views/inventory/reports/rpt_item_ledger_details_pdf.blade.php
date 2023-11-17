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
              <tr><th align="center" width="100%" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="5"><font size="2"><b>Item Ledger (Details)</b><font></th></tr>
              <tr><th align="left" colspan="4"><b>{{$item->item_name}}({{$item->itm_cat_origin}}{{$item->itm_cat_name}})</b></th>
              <th align="right" colspan="1"><font size="3"><b>Date:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>

          <tr>
            <th class="text-center">Date</th>
            <th class="text-center">Transaction No</th>
            <th class="text-center">Desc</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php $OPP = 0; $balance = 0; ?>
          @foreach($opening as $op)
            <?php $OPP = $op->op ?>
            <tr>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">Opening</td>
              <td align="right">{{ number_format(abs($op->op),2) }}</td>
              <td align="center">&nbsp;</td>
            </tr>
          @endforeach

          @foreach($transactions as $row)
          <?php   $balance = $balance + $OPP + $row->qty; ?>
          <tr>
            <td align="center" width="12%">{{ date('d-m-Y',strtotime($row->item_op_dt)) }}</td>
            <td align="center" width="20%">{{ $row->item_trans_desc }}-{{ $row->item_trans_ref_no }}</td>
            <td align="center" width="15%">{{ $row->item_trans_desc }}</td>
            <td align="right" width="15%">{{ number_format($row->qty,2) }}</td>
            <td align="center" width="35%">&nbsp;</td>
          </tr>
          <?php $OPP =0 ; ?>
          @endforeach

          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center"><b>Balance:</b></td>
            <td align="right"><b>{{ number_format(abs( $balance ),2) }}</b></td>
            <td align="center">&nbsp;</td>
          </tr>
          </tbody>
        </table>
        </div>
  </div>

</div>
</section>
</body>
</html>
