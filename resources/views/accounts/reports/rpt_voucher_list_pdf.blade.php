<!DOCTYPE>
<html>
<head>
    <title>Voucher Report</title>
    <style>
    body { margin: 0; font-size: 14px; font-family: "Arrial Narrow";}

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
      width: 98%;
      border-collapse: collapse;
    }
    </style>
</head>
<body>
<section class="content">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <table align="center">
        <thead>
            <tr><th class="text-center" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
            <tr><th class="text-center" colspan="5"><font size="3"><b>Voucher Report</b><font></th></tr>
            <tr><th align="right"  colspan="5"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-bordered table-report">
        <thead class="thead-dark">
          <tr>
            <td align="center"><b>Voucher No</b></td>
            <td align="center"><b>Account Name</b></td>
            <td align="center"><b>Narration</b></td>
            <td align="center"><b>Debit</b></td>
            <td align="center"><b>Credit</b></td>
          </tr>
        </thead>
        <tbody> <?php $dt = ''; $total_Debit = 0;   $total_Credit = 0; ?>
          @foreach($vouchers as $row)
          <?php
          $total_Debit = $total_Debit + $row->d_amount;
          $total_Credit = $total_Credit + $row->c_amount;
          if($dt == '' || $dt != $row->voucher_date) { ?>
            <tr>
              <td colspan="5"><b>{{ date('d/m/Y', strtotime($row->voucher_date)) }}</b></td>
            </tr>
          <?php } ?>
          <tr>
            <td width="8%">{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            <td width="20%">{{ $row->acc_head }}</td>
            <td width="20%">{{$row->trans_type == 'SV'?'Invoice-':''}}{{$row->acc_invoice_no}} {!! $row->t_narration !!}</td>
            <td width="10%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'':number_format($row->d_amount,2) }}</td>
            <td width="10%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'':number_format($row->c_amount,2) }}</td>
          </tr>
          <?php $dt = $row->voucher_date; ?>
          @endforeach
          <tr>
            <td colspan="3" align="right"><b>Total:&nbsp;&nbsp;</b></td>
            <td width="10%" align="right"><b>&nbsp;&nbsp;{{ number_format($total_Debit,2) }}</b></td>
            <td width="10%" align="right"><b>&nbsp;&nbsp;{{ number_format($total_Credit,2) }}</b></td>
          </tr>
          </tbody>
        </table>
        </div>

    </div>

</div>
</section>
</body>
</html>
