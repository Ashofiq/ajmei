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
                <tr><td colspan="5" align="center"><font size="5">Customer Wise Price Report</font></td></tr>

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
                          <td align="center" scope="col"><b>Customer Id</b></td>
                          <td align="center" scope="col"><b>Name</b></td>
                          <td align="center" scope="col"><b>Addresso</b></td>
                          <td align="center" scope="col"><b>District</b></td>
                        </tr>
                      </thead>
                      <tbody>
                        @if ($customer_data->count())
                            @foreach($customer_data as $d)
                            <tr>
                              <td align="center">{{ $d->cust_name }}</td>
                              <td align="center">{{ $d->cust_code }}</td>
                              <td align="center">{{ $d->cust_add1 }} {{ $d->cust_add2 }}</td>
                              <td align="center">{{ $d->vCityName }}</td>
                            </tr>
                          @endforeach
                        @else
                            <tr>
                              <td align="center">&nbsp;</td>
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
                   <td align="center"><b>Item Code</b></td>
                   <td align="center"><b>Item Name</b></td>
                   <td align="center"><b>Price</b></td>
                   <td align="center"><b>Valid</b></td>
                   <td align="center"><b>To</b></td>
                </tr>
                @foreach($rows as $row)
                  <tr>
                     <td>{{ $row->item_code }}</td>
                     <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td>
                     <td align="right">{{ $row->cust_price }}</td>
                     <td align="center">{{date('d-m-Y',strtotime($row->p_valid_from))}}</td>
                     <td align="center">{{date('d-m-Y',strtotime($row->p_valid_to))}}</td>
                  </tr>
                @endforeach
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
