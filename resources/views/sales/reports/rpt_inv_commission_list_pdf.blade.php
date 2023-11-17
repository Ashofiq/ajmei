<html>
    <head>
        <style>
        body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";}
            /** Define the margins of your page **/
            @page {
                margin: 100px 55px;
            }

            header {
                position: fixed;
                top: -45px;
                left: 0px;
                right: 0px;
                height: 50px;
                /** Extra personal styles **/

                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed;
                bottom: 10px;
                text-align: center;
            }

            table {
              width: 100%;
              border-collapse: collapse;
            }
            h1 {
              border-bottom-style: solid;
            }
        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
          <div class="row justify-content-center">
            <div class="col-md-12">
              <table class="table" >
                <tr><td colspan="5" align="center"><font size="5">Sales Commission Report</font></td></tr>
                <tr><td colspan="5" align="center"><font size="5">{{ date('d-m-Y', strtotime($fromdate)) }} TO
                {{ date('d-m-Y', strtotime($todate)) }}</font></td></tr>
              </table>
            </div>
          </div>
        </header>

        <footer>
          <div class="row">
            <div class="col-sm-12">
              <table border="0" cellspacing="0" cellpadding="0"
              style='font-family:"Arrial Narrow", Courier, monospace; font-size:80px'>

                  <tr>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td height="40%">&nbsp;</td>
                  </tr>

              </table>
            </div>
          </div>
        </footer>
        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p>
              <div class="row justify-content-center">
                <div class="col-md-12">
                <table class="table table-bordered" border="1">
                 <tr>
                   <td align="center"><b>Invoice&nbsp;Date</b></td>
                   <td align="center"><b>Invoice No</b></td>
                   <td align="center"><b>Sales Voucher</b></td>
                   <td align="center"><b>Customer</b></td>
                   <td align="center"><b>Voucher</b></td>
                   <td align="center"><b>Net Amount</b></td>
                   <td align="center"><b>Commission</b></td>
                </tr>
                <tbody>
                 <?php
                    $total_invnetamt = 0;
                    $total_invcomm = 0;
                 ?>
                  @foreach($rows as $row)
                  <?php
                    $total_invnetamt += $row->inv_netamt;
                    $total_invcomm += $row->commission;
                  ?>
                  <tr>
                    <td>{{date('d-m-y',strtotime($row->inv_date))}}</td>
                    <td>{{ $row->inv_no }}</td>
                    <td>{{ $row->trans_type }} {{ $row->voucher_no }}</td>
                    <td>{{ $row->cust_name }}</td>
                    <td>{{ $row->inv_acc_voucher }}</td>
                    <td align="right">{{ number_format($row->inv_netamt,2) }}</td>
                    <td align="right">{{ number_format($row->commission,2) }}</td>
                  </tr>
                  @endforeach
                  <tr>
                    <td colspan="5">&nbsp;</td>
                    <td align="right"><b>{{ number_format($total_invnetamt,2) }}</b></td>
                    <td align="right"><b>{{ number_format($total_invcomm,2) }}</b></td>
                  </tr>
                  </tbody>
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
