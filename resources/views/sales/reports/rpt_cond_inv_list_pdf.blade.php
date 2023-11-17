<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 11px; font-family: "Arrial Narrow";}

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
      .center {
        margin-left: auto;
        margin-right: auto;
      }
  }

  footer {
      position: fixed;
      bottom: 30px;
      text-align: center;
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
    <title>Sales Conditional Report</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">
    <div class="col-md-12">
      <table>
          <thead>
              <tr><th align="center" colspan="11"><font size="4"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="11"><font size="3"><b>Sales Conditional Report</b><font></th></tr>
              <tr><th align="center" colspan="11"><font size="3">{{ date('d-m-Y', strtotime($fromdate)) }} TO
              {{ date('d-m-Y', strtotime($todate)) }}</font></th></tr>
          <tr>
            <td align="center"><b>Delivery To</b></td>
            <td align="center"><b>Date</b></td>
            <td align="center"><b>Invoice No</b></td>
            <td align="center"><b>Ref No</b></td>
            <td align="center"><b>Customer</b></td>
            <td align="center"><b>Voucher No</b></td>
            <td align="center"><b>Comments</b></td>
            <td align="center"><b>Total Amount</b></td>
            <td align="center"><b>Total Disc</b></td>
            <td align="center"><b>Total VAT</b></td>
            <td align="center"><b>Net Amount</b></td>
          </tr>
          </thead>
        <tbody>
         <?php
            $courrier_to    = '';
            $sub_inv_sub_total  = 0;
            $sub_inv_disc_value = 0;
            $sub_inv_vat_value  = 0;
            $sub_inv_net_amt    = 0;

            $inv_sub_total  = 0;
            $inv_disc_value = 0;
            $inv_vat_value  = 0;
            $inv_net_amt    = 0;
         ?>
          @foreach($rows as $row)
           <?php

            $inv_sub_total  += $row->inv_sub_total;
            $inv_disc_value += $row->inv_disc_value;
            $inv_vat_value  += $row->inv_vat_value;
            $inv_net_amt    += $row->inv_net_amt;
          ?>

          @if($courrier_to != '' && $courrier_to != $row->courrier_to)
          <tr>
            <td align="right" colspan="7"><b>Sub Total :</b></td>
            <td align="right"><b>{{ number_format($sub_inv_sub_total,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_inv_disc_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_inv_vat_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_inv_net_amt,2) }}</b></td>
          </tr>
          <?php
          $sub_inv_sub_total  = 0;
          $sub_inv_disc_value = 0;
          $sub_inv_vat_value  = 0;
          $sub_inv_net_amt    = 0;  ?>
          @endif

          @if($courrier_to == '' || $courrier_to != $row->courrier_to)
          <tr>
            <td colspan="4"><b>{{ $row->courrier_to }}</b></td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td><td></td>
          </tr>
          @endif

          <tr>
            <td>&nbsp;</td>
            <td>{{ date('d-m-Y',strtotime($row->inv_date)) }}</td>
            <td>{{ $row->inv_no }}</td>
            <td>{{ $row->inv_del_ref }}</td>
            <td>{{ $row->cust_name }}</td>
            <td>{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            <td>{{ $row->inv_del_comments }}</td>
            <td align="right">{{ number_format($row->inv_sub_total,2) }}</td>
            <td align="right">{{ number_format($row->inv_disc_value,2) }}</td>
            <td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>

          </tr>
          <?php $courrier_to = $row->courrier_to;
          $sub_inv_sub_total  += $row->inv_sub_total;
          $sub_inv_disc_value += $row->inv_disc_value;
          $sub_inv_vat_value  += $row->inv_vat_value;
          $sub_inv_net_amt    += $row->inv_net_amt;
          ?>
          @endforeach

          <tr>
            <td align="right" colspan="7"><b>Sub Total :</b></td>
            <td align="right"><b>{{ number_format($sub_inv_sub_total,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_inv_disc_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_inv_vat_value,2) }}</b></td>
            <td align="right"><b>{{ number_format($sub_inv_net_amt,2) }}</b></td>
          </tr>

          <tr>
            <td align="right" colspan="7"><b>Grand Total :</b></td>
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
</section>

<footer>

      <table border="0" align="center" style="width:70%;">
        <tr>

          <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
                    <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
                    <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
           <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;CEO&nbsp;&nbsp;&nbsp;</td>
           <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
           <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
           <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
           <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;text-align:center;">Managing Director</td>
           <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;border-top:1px solid white;text-align:center;">&nbsp;&nbsp;&nbsp;</td>
        </tr>
      </table>

</footer>

</body>
</html>
