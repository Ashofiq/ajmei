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
      border: 1px solid black;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }
  </style>
    <title>Invoice Wise Report</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">

    <div class="row">
        <h4 style=" text-align: center;">Invoice Wise Report</h4>
    </div>
    <div class="col-md-12">
        <table class="table " width="100%" style="font-family: serif; margin-top:-10px;">
            <thead class="thead-blue">
                <tr>
                    <th class="text-center" scope="col">Date</th>
                    <th class="text-center" scope="col">Invoice No</th>
                    <!--th class="text-center" scope="col">Delivery No</th>
                    <th class="text-center" scope="col">Sales Order</th -->
                    <th class="text-center" scope="col">Customer</th>
                    <th class="text-center" scope="col">Total Amount</th>
                    <th class="text-center" scope="col">Total Disc</th>
                    <th class="text-center" scope="col">Total VAT</th>
                    <th class="text-center" scope="col">Net Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            
                $inv_sub_total = 0;
                $inv_disc_value = 0;
                $inv_vat_value=0;
                $inv_net_amt = 0;
            ?>
            @foreach($rows as $row)
            <?php 
                $inv_sub_total += $row->inv_sub_total;
                $inv_disc_value += $row->inv_disc_value;
                $inv_vat_value += $row->inv_vat_value;
                $inv_net_amt += $row->inv_net_amt;
                
            ?>
            <tr>
                <td>{{ $row->inv_date }}</td>
                <td>{{ $row->inv_no }}</td>
                <!-- td>{{ $row->inv_so_po_no }}</td>
                <td>{{ $row->inv_so_po_no }}</td -->
                <td>{{ $row->cust_name }}</td>
                <td align="right">{{ $row->inv_sub_total }}</td>
                <td align="right">{{ $row->inv_disc_value }}</td>
                <td align="right">{{ $row->inv_vat_value }}</td>
                <td align="right">{{ $row->inv_net_amt }}</td>
                
            </tr>
            @endforeach
            <tr> 
                <td colspan="3">&nbsp;</td>
                <td align="right"><b>{{ number_format($inv_sub_total,2) }}</b></td>
                <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></b></td>
                <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
                <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
            
            </tr>
            </tbody>
        </table>
    </div>

    </div>

  </div>

</div>
</section>
</body>
</html>
