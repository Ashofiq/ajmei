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
                <tr><td colspan="5" align="center"><font size="5">Sales Person Wise Customer Report</font></td></tr>

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
                     <thead class="thead-blue">
                        <tr>
                          <td align="center" scope="col"><b>Name</b></td>
                          <td align="center" scope="col"><b>Designation</b></td>
                          <td align="center" scope="col"><b>Cell No</b></td>
                        </tr>
                      </thead>
                      <tbody>
                        @if ($sales_person_data->count())
                            @foreach($sales_person_data as $d)
                            <tr>
                              <td align="center">{{ $d->sales_name }}</td>
                              <td align="center">{{ $d->vComboName }}</td>
                              <td align="center">{{ $d->sales_mobile }}</td>
                            </tr>
                          @endforeach
                        @else
                            <tr>
                              <td align="center">&nbsp;</td>
                              <td align="center">&nbsp;</td>
                              <td align="center">&nbsp;</td>
                            </tr>
                        @endif
                      </tbody>
                  </table>
                </div>
                <div class="col-md-12">
                <table class="table table-bordered" border="1">
                 <tr>
                   <td align="center"><b>Code</b></td>
                   <td align="center"><b>Name</b></td>
                   <td align="center"><b>Address</b></td>
                   <td align="center"><b>Phone</b></td>
                </tr>
                @foreach($rows as $row)
                  <tr>
                    <td align="center">{{ $row->cust_slno }}</td>
                    <td align="left">{{ $row->cust_name }}</td>
                    <td align="left">{{ $row->cust_add1 }} {{ $row->cust_add2 }}</td>
                    <td align="left">{{ $row->cust_mobile }} {{ $row->cust_phone }}</td>
                  </tr>
                  @endforeach
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
