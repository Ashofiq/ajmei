<!DOCTYPE>
<html>
<head>
  <style>
    body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";}

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
              <tr><th align="center" width="100%" colspan="10"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="10"><font size="2"><b>Item Wise Purchase Report</b><font></th></tr>
              <tr><th align="right" colspan="10"><font size="3"><b>Date Range:&nbsp;{{ date('d-m-Y', strtotime($fromdate)) }} to {{ date('d-m-Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>

          <tr>
            <td align="center" width="10%"><b>Date</b></td>
            <td align="center" width="15%"><b>Supplier</b></td>
            <td align="center" width="7%"><b>PO No</b></td>
            <td align="center" width="30%"><b>Item Category</b></td>
            <!-- <td align="center" width="9%"><b>Item Code</b></td> -->
            <td align="center" width="9%"><b>Item Name</b></td>
            <!-- <td align="center" width="9%"><b>Lot No</b></td> -->
            <td align="center" width="9%"><b>Price</b></td>
            <td align="center" width="9%"><b>Qty</b></td>
            <td align="center" width="13%"><b>Amount</b></td>
          </tr>
        </thead>
        <tbody>
          <?php
            $pur_order_no = '';
            $sub_qty    = 0;
            $sub_amount = 0;
            $total_qty  = 0;
            $total_amount = 0;
          ?>
          @foreach($receives as $row)
          <?php
            $total_qty += $row->raw_total_qty;
            $total_amount += $row->raw_total_qty*$row->raw_item_price;
          ?>
          @if($pur_order_no != '' && $pur_order_no != $row->pur_order_no)
          <tr>
            <td align="right" colspan="3"><b>Sub Total :</b></td>
            <td align="right"><b>&nbsp;</b></td>
            <td align="right"><b>{{ number_format($sub_qty,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_amount,2) }}</b></td>
          </tr>
          <?php $sub_qty = 0; $sub_amount = 0; ?>
          @endif
          @if($pur_order_no == '' || $pur_order_no != $row->pur_order_no)
          <!-- <tr>
            <td align="center">{{ date('d-m-Y',strtotime($row->pur_order_date)) }}</td>
            <td align="center">{{ $row->supp_name }}</td>
            <td align="center">{{ $row->pur_order_no }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
           
          </tr> -->
          @endif
          <tr>
          <td align="center">{{ date('d-m-Y',strtotime($row->pur_order_date)) }}</td>
            <td align="center">{{ $row->supp_name }}</td>
            <td align="center">{{ $row->raw_order_no }}</td>
            <td>{{ $row->itm_cat_origin }} {{ $row->itm_cat_name }}</td>
            <!-- <td align="center">{{ $row->item_code }}</td> -->
            <td> {{ $row->item_name }}</td>
            <!-- <td>{{ $row->pur_lot_no }}</td> -->
            <td align="right">{{ number_format($row->raw_item_price,2) }}</td>
            <td align="right">{{ number_format($row->raw_total_qty,2) }}</td>
            <td align="right">{{ number_format($row->raw_item_price*$row->raw_total_qty,2) }}</td>
          </tr>
          <?php $pur_order_no = $row->pur_order_no;
          $sub_qty += $row->raw_total_qty;
          $sub_amount += $row->raw_total_qty*$row->raw_item_price;?>
          @endforeach
          <!-- <tr>
            <td align="right" colspan="5"><b>Sub Total :</b></td>
            <td align="right"><b>&nbsp;</b></td>
            <td align="right"><b>{{ number_format($sub_qty,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_amount,2) }}</b></td>
          </tr> -->
          <tr>
            <td align="right" colspan="6"><b>Grand Total :</b></td>
            <!-- <td align="right"><b>&nbsp;</b></td> -->
            <td align="right"><b>{{ number_format($total_qty,2) }}</b></td>
            <td align="right"><b>{{ number_format($total_amount,2) }}</b></td>
          </tr>
          </tbody>
        </table>
        </div>
  </div>

</div>
</section>
</body>
</html>
