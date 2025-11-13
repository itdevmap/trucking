<html>

<head>
    <title>{{ $sales->code }}</title>
    <style>
        * {
            font-size: 10px;
            line-height: 1.2;
            font-family: Consolas, Menlo, Monaco, "Lucida Console", "Courier New", monospace;
        }

        body {
            align-content: flex-start;
        }

        hr {
            margin: 0;
            border-top: 1px solid;
        }

        @page {
            size: 58mm 297mm;
            margin: 1;
        }

        @media print {
            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }

        table {
            border-collapse: collapse;
            border: 0px;
        }

        table tr#table-info td {
            text-align: left;
            vertical-align: top;
        }z

        body, * {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>


{{-- @dd($pos_sales_details) --}}

<body>
    <button class="hidden-print" onclick="balikKasir()">Kembali</button>
    <button class="hidden-print" onclick="window.close()" autofocus>Tutup (Q)</button>
    <button class="hidden-print" onclick="window.print()">Print (P)</button>
    <button type="button" class="hidden-print btn50ms" data-code="{{ $sales->code }}">Print 50ms</button>

    <div class="print" style="width: 240px">
        <div style="text-align: center">
            <img src="{{ asset('itp_horizontal.png') }}" alt="Logo" style="width: 110px; margin: auto;">
            {{-- <img src="{{ asset('logoPT4.png') }}" alt="Logo" style="width: 110px; margin: auto;"> --}}
        </div>

        <table style="width: 230px; margin-top: 10px">
            <tbody>
                {{-- <tr>
                    <td colspan="12" align="center" style="font-size: 16px; font-weight: bold">
                        {{$textPrint->print_header1}} <br> 
                        {{$textPrint->print_header2}}
                    </td>
                </tr> --}}
                <tr>
                    <td colspan="10" align="center">
                        {{$textPrint->print_header3}}
                        {{-- Dupak Center, Jl. Gundih Blok A, Kec. Bubutan, Surabaya, Jawa Timur 60172 --}}
                    </td>
                </tr>
                <tr><td colspan="12"><hr></td></tr>

                @if ($reprint === "yes")
                    <tr id="table-info">
                        <td>Reprint</td><td>:</td><td colspan="1">Yes</td>
                    </tr>
                @endif

                <tr id="table-info">
                    <td >No</td>
                    <td>:</td>
                    <td colspan="1">{{ substr($sales->code, -4) }}</td>

                    <td>Tgl</td>
                    <td>:</td>
                    <td>{{ date('d/M/Y', strtotime($sales->date_order)) }}</td>
                    <td>{{ date('H:i', strtotime($sales->created_at)) }}</td>
                </tr>
                {{-- <tr id="table-info">
                    <td>Kode</td><td>:</td><td colspan="2">{{ $sales->customer }}</td>
                </tr> --}}
                @if ($sales->return_code)
                <tr id="table-info">
                    <td>Kode Refund</td><td>:</td><td colspan="1">{{ substr($sales->return_code, -4) }}</td>
                </tr>
                @endif
                <tr id="table-info">
                    <td>Kasir</td><td>:</td><td colspan="6">{{ $nama_kasir->full_name }}</td>
                </tr>
                <tr id="table-info">
                    <td>Member</td><td>:</td><td colspan="4">{{ !empty($sales->member_phone) ? $sales->member_phone : 'Non Member' }}</td>
                </tr>
                <tr id="table-info">
                    <td>Pay Method</td><td>:</td><td colspan="4">{{ $paymentMethod }}</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 230px;">
            <tbody>
                <tr><td colspan="6"><hr></td></tr>
                @foreach ($pos_sales_details as $sales_detail)
                    <tr>
                        <td colspan="12">{{ \Illuminate\Support\Str::limit($sales_detail->short_itemname ?? $sales_detail->item_name, 45) }}</td>
                    </tr>
                    <tr>
                        <td align="right" width="40">{{ number_format($sales_detail->quantity, 0, ',', ',') }}</td>
                        <td colspan="2" align="right" width="80">{{ number_format($sales_detail->sell_price, 0, ',', ',') }}</td>
                        <td colspan="3" align="right" width="100">{{ number_format($sales_detail->total_item, 0, ',', ',') }}</td>
                    </tr>
                    @if ($sales_detail->sub_total < 1)
                        <tr>
                            <td colspan="3" style="text-align: right">Hemat</td>
                            <td colspan="3" align="right">-{{ number_format($sales_detail->disc_promo + $sales_detail->disc1_nominal, 0, ',', ',') }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr><td colspan="6"><hr></td></tr>

                <tr><td colspan="4" align="right">Total :</td><td colspan="2" align="right">{{ number_format($hemat_item, 0, ',', ',') }}</td></tr>
                {{-- <tr><td colspan="4" align="right">Hemat Pembelian :</td><td colspan="2" align="right">{{ number_format($sales->disc_nominal, 0, ',', ',') }}</td></tr> --}}
                {{-- <tr><td colspan="4" align="right">Hemat Produk :</td><td colspan="2" align="right">{{ number_format($pos_sales_details->disc_promo, 0, ',', ',') }}</td></tr> --}}
                {{-- <tr><td colspan="4" align="right">Voucher :</td><td colspan="2" align="right">{{ number_format($sales->voucher, 0, ',', ',') }}</td></tr> --}}

                @if ($sales->total_refund != 0)
                <tr><td colspan="4" align="right">Total Refund :</td><td colspan="2" align="right">{{ number_format($sales->total_refund, 0, ',', ',') }}</td></tr>
                @endif

                {{-- <tr>
                    <td colspan="4" align="right">Total Refund :</td>
                    <td colspan="2" align="right">{{ number_format($totRefund, 0, ',', ',') }}</td>
                </tr> --}}
                <tr>
                    <td colspan="4" align="right">Bayar :</td>
                    <td colspan="2" align="right">{{ number_format($sales->payment - $sales->voucher, 0, ',', ',') }}</td>
                </tr>
                <tr><td colspan="4" align="right">Kembali :</td><td colspan="2" align="right">{{ number_format($sales->cashBack, 0, ',', ',') }}</td></tr>

                <tr><td colspan="6" style="font-size: 8px">*harga sudah termasuk pajak</td></tr>
                <tr><td colspan="6"><hr></td></tr>
            </tbody>
        </table>

        <div style="width: 230px;">
            <div style="display: flex; text-align: center;">
                <div>
                    {{$textPrint->print_footer1}}
                </div>
            </div>
            <div style="text-align: center">
                <h6 style="margin-bottom: 5px;">{{$textPrint->print_footer2}} 
                </h6>
                <div style="text-align: center">
                    <img src="{{ asset($textPrint->print_footer3) }}" alt="Logo" style="width: 150px; margin: auto;">
                    {{-- <img src="{{ asset('toyfiesta.png') }}" alt="Logo" style="width: 110px; margin: auto;"> --}}
                    
                    {{-- <img src="{{ asset('itp_horizontal.png') }}" alt="Logo" style="width: 110px; margin: auto;"> --}}
                </div>
            </div>
            <div style="display: flex; flex-direction: row; margin-top: 1rem; gap: 10px;">
                <div style="width: 70%;">
                    <div style="display: flex;">
                        <strong style="width: 40px;">WA</strong>
                        {{-- <span>{{$textPrint->wa}}</span> --}}
                        <span>+62 851-8328-9797</span>
                    </div>
                    <div style="display: flex;">
                        <strong style="width: 40px;">IG</strong>
                        {{-- <span>{{$textPrint->ig}}</span> --}}
                        <span>@indonesiatoysparadise</span>
                    </div>
                    <div style="display: flex;">
                        <strong style="width: 40px;">Tiktok</strong>
                        {{-- <span>{{$textPrint->tt}}</span> --}}
                        <span>toysparadise_id</span>
                    </div>
                </div>

                <div style="width: 30%; text-align: center;">
                    {{-- <img src="{{ asset('barQR.jpg') }}" alt="QR" style="width: 50px;"> --}}
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    function balikKasir() {
        location.replace('/sales/create');
    }

    document.addEventListener("keypress", function(event) {
    console.log(event.keyCode);
        if (event.keyCode == 113) {
            window.close()
        }
        if (event.keyCode == 112) {
            window.print()

        }
    });
    document.addEventListener("DOMContentLoaded", function(load) {

        // detek
        // detect()
        // cetak
        window.print();
        // ngapain sebelum print
        window.onbeforeprint = (event) => {
            console.log('counter_print_here');
        };
        // setelah print

        window.addEventListener("afterprint", function(event) {
            // balikKasir()

        });
        window.onafterprint = function(event) {
            // balikKasir()
        };
    });


document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn50ms').forEach(function(btn) {
        btn.addEventListener('click', function() {
            let code = this.dataset.code;
            window.open('/sales/print50ms/' + encodeURIComponent(code), '_blank');
        });
    });
});
</script>

</html>