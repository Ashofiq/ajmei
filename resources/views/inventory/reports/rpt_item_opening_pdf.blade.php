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
              <tr><th align="center" width="100%" colspan="7"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="7"><font size="2"><b>Item Opening Report</b><font></th></tr>
              <tr><th align="right" colspan="7"><font size="3"><b>Date Range:&nbsp;{{ date('d-m-Y', strtotime($fromdate)) }} to {{ date('d-m-Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>
          
          <tr> 
            <td align="center" width="15%"><b>Warehoue</b></td>
            <td align="center" width="25%"><b>Item Category</b></td>
            <td align="center" width="10%"><b>Item Code</b></td>
            <td align="center" width="25%"><b>Item Name</b></td>
            <td align="center" width="15%"><b>Opening</b></td>
            <td align="center" width="15%"><b>Price</b></td>
            <td align="center" width="15%"><b>Amount</b></td>
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
</body>
</html>
