<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 11px; font-family: "Arrial Narrow";}

  @media print {
      *{
          font-family: "Arrial Narrow" !important;
      }
      .header, .header-space,
      .footer, .footer-space {
      }
      .wrapper{
          margin-top: 50px;
      }
      .header {
          position: fixed;
          top: -55px;
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
    <title>Sales Person Wise Sales</title>

</head>
<body>
  <div class="row justify-content-center">
    <div class="col-md-12">
      <table>
        <thead>
          <tr><th colspan="7" align="center"><font size="5">Sales Persons Wise Sales Summary</font></th></tr>
          <tr><th colspan="7" align="center"><font size="3">{{ date('d-m-Y', strtotime($fromdate)) }} TO
          {{ date('d-m-Y', strtotime($todate)) }}</font></th></tr>

          <tr>
            <td align="center"><b>SL</b></td>
            <td align="center"><b>Sales Person</b></td>
            <td align="center"><b>Total Sales</b></td>
            <td align="center"><b>Received</b></td>
            <td align="center"><b>VAT</b></td>
            <td align="center"><b>Outstanding</b></td>
          </tr>
        </thead>
        <tbody>
            <?php
            $i=1;
            $inv_net_amt = 0;
            $collection = 0;
            $inv_vat_value = 0;
            $outstanding = 0;
         ?>
          @foreach($rows as $row)
          <?php 
            $inv_net_amt += $row->inv_net_amt;
            $collection += $row->collection;
            $inv_vat_value += $row->inv_vat_value;
            $outstanding += $row->outstanding;
          ?>
          <tr>
            <td align="center">{{ $i++ }}</td>
            <td>{{ $row->sales_name }}</td>
            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
            <td align="right">{{ number_format($row->collection,2) }}</td>
            <td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
            <td align="right">{{ number_format($row->outstanding,2) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="2"><b>Total:</b></td>
            <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
            <td align="right"><b>{{ number_format($collection,2) }}</b></td>
            <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($outstanding,2) }}</b></td>
          </tr>
          </tbody>
        </table>
        </div>
  </div>

</body>
</html>
