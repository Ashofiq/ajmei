<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 15px; font-family: "Arrial Narrow";}


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
    <title>Subsidiary Ledger</title>

</head>
<body>
<section class="content">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <table width="95%" align="center">
        <thead>
          <tr><th align="center" colspan="6"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
          <tr><th align="center" colspan="6"><font size="3"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th></tr>
          <tr><th align="left" colspan="5"><b>Head Of Accounts:&nbsp;{{$ledgername}}</b></th>
           <th align="middle" colspan="1"><b>Subsidiary<br/>Ledger</b></th></tr>
           <tr><th align="left" colspan="6"><b>Address:&nbsp;{{empty($cust_data)?'':$cust_data->cust_add1}} {{empty($cust_data)?'':$cust_data->cust_add2}}</b></th></tr>
        </thead>

          <tr>
            <td align="center"><b>Date</b></td>
            <td align="center"><b>Invoice No</b></td>
            <td align="center"><b>Description</b></td>
            <td align="center"><b>Debit</b></td>
            <td align="center"><b>Credit</b></td>
            <td align="center"><b>Balance</b></td>
          </tr>
        <tbody>
          <tr>
            <?php
            $opening = $opening->debit - $opening->credit;
            $total_Debit = 0;   $total_Credit = 0;
            ?>
            <td colspan="5"><b>Opening Balance :</b></td>
            <td width="10%" align="right">&nbsp;{{ number_format($opening,2) }}</td>
          </tr>
          @foreach($rows as $row)
          <?php $balance = $opening + $row->d_amount - $row->c_amount;
          $total_Debit = $total_Debit + $row->d_amount;
          $total_Credit = $total_Credit + $row->c_amount; ?>
          <tr>
            <td style="width: 70px;">{{ date('d/m/Y', strtotime($row->voucher_date)) }}&nbsp;&nbsp;</td>
            <td style="width: 100px;">&nbsp;{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            <td style="width: 380px;">{{$row->trans_type == 'SV'?'Invoice-':''}}{{$row->acc_invoice_no}} {!! $row->t_narration !!}</td>
            <td style="width: 180px;" align="right">{{ number_format($row->d_amount,2)=='0.00'?'':number_format($row->d_amount,2) }}</td>
            <td style="width: 180px;" align="right">{{ number_format($row->c_amount,2)=='0.00'?'':number_format($row->c_amount,2) }}</td>
            <td style="width: 180px;" align="right">{{ number_format($balance,2) }}</td>
          </tr>
          <?php $opening = $balance; ?>
          @endforeach
          <tr>
            <td colspan="3" align="right"><b>Total:&nbsp;&nbsp;</b></td>
            <td align="right"><b>&nbsp;&nbsp;{{ number_format($total_Debit,2) }}</b></td>
            <td align="right"><b>&nbsp;&nbsp;{{ number_format($total_Credit,2) }}</b></td>
            <td align="right"><b>&nbsp;&nbsp;</b></td>
          </tr>
          </tbody>
        </table>
        </div>
  </div>
</div>
</section>
</body>
</html>
