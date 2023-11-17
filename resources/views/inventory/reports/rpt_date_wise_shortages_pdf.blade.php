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
              <tr><th align="center" width="100%" colspan="6"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="6"><font size="2"><b>Date Wise Shortage Report</b><font></th></tr>
              <tr><th align="right" colspan="6"><font size="3"><b>Date Range:&nbsp;{{ date('d-m-Y', strtotime($fromdate)) }} to {{ date('d-m-Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>

          <tr>
            <td align="center" width="10%"><b>Date</b></td>
            <td align="center" width="9%"><b>Item Category</b></td>
            <td align="center" width="9%"><b>Item Code</b></td>
            <td align="center" width="9%"><b>Item Name</b></td>
            <td align="center" width="9%"><b>Lot No</b></td>
            <td align="center" width="9%"><b>Qty</b></td>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td align="center">{{ date('d-m-Y',strtotime($row->short_date)) }}</td>
            <td>{{ $row->itm_cat_name }}</td>
            <td align="center">{{ $row->item_code }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->short_lot_no }}</td>
            <td align="right">{{ number_format($row->short_item_qty,2) }}</td>
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
