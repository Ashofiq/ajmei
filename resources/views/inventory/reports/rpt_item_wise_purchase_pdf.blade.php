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
              <tr><th align="center" width="100%" colspan="9"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="9"><font size="2"><b>Item Wise Purchase Report</b><font></th></tr>
              <tr><th align="right" colspan="9"><font size="3"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>

          <tr>
            <td align="center" width="10%"><b>Date</b></td>
            <td align="center" width="10%"><b>PI No</b></td>
            <td align="center" width="15%"><b>PO No</b></td>
            <td align="center" width="15%"><b>Supplier</b></td>
            <td align="center" width="9%"><b>Item Code</b></td>
            <td align="center" width="9%"><b>Item Name</b></td>
            <!-- <td align="center" width="9%"><b>Lot No</b></td> -->
            <td align="center" width="9%"><b>Price</b></td>
            <td align="center" width="9%"><b>Qty</b></td>
            <td align="center" width="9%"><b>Amount</b></td>
          </tr> 
        </thead>
        <tbody>
        <?php $qty = 0; $price = 0; $amount = 0;  ?>

          @foreach($receives as $row)
          <tr>
            <td align="center">{{ date('d-m-Y',strtotime($row->raw_order_date)) }}</td>
            <td align="center">{{ $row->raw_pi_no }}</td>
            <td align="center">{{ $row->rawmetarial->raw_order_no }}</td>
            <td align="center">{{ $row->supp_name }}</td>
            <td align="center">{{ $row->item->item_code }}</td>
            <td>{{ $row->item->item_name }}</td>
            <!-- <td>{{ $row->raw_lot_no }}</td> -->
            <td align="right">{{ number_format($row->raw_item_price,2) }}</td>
            <td align="right">{{ number_format($row->raw_item_qty,2) }}</td>
            <td align="right">{{ number_format($row->raw_item_price*$row->raw_item_qty,2) }}</td>

            <!-- old -->
            <!-- <td align="center">{{ $row->pur_order_no }}</td>
            <td>{{ $row->itm_cat_name }}</td>
            <td align="center">{{ $row->item_code }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->pur_lot_no }}</td>
            <td align="right">{{ number_format($row->pur_item_price,2) }}</td>
            <td align="right">{{ number_format($row->pur_item_qty,2) }}</td>
            <td align="right">{{ number_format($row->pur_item_price*$row->pur_item_qty,2) }}</td> -->
          </tr>
          <?php $qty += $row->raw_item_qty; $price += $row->raw_item_price; $amount += $row->raw_item_price*$row->raw_item_qty;  ?>

          @endforeach

          <tr>
                <td align="center"></td>
                <td align="center"></td>
                <td></td>
                <td align="center"></td>
                <td></td>
                <td align="right"><b>Total</b></td>
                <td align="right"><b>{{ $price }}</b></td>
                <td align="right"><b>{{ number_format($qty, 2) }}</b></td>
                <td align="right"><b>{{ number_format($amount, 2) }}</b></td>
              </tr>
          </tbody>
        </table>
        </div>
  </div>

</div>
</section>
</body>
</html>
