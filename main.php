<div class="card-body">
    <h4 class="card-title">Halaman Utama</h4>

    <table class="table table-sm mb-3">
        <tbody>
            <tr>
                <td width="3" height="400" align="center" style="background-image: url(images/dots.gif); background-repeat: repeat-y; width: 3px;">
                    <div>&nbsp;</div>
                </td>
                <td width="100%" valign="top">
                    <div class="maroon" align="left"><b>Halaman Utama</b></div>


                    <form name="MyForm" action="/ikoop/[NAMA KOPERASI]/index.php?page=main&page_id=1&rec_per_page=10&sort=DESC" method="post">
                        <div class="">
                            <table class="table table-sm mb-3" width="100%" align="center" style="background_image: ">
                                <tr>
                                    <td>
                                        <font class="maroonText">Selamat Datang!</font>
                                        <hr size="1px">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>Selamat datang ke Sistem E-iKOOP [NAMA KOPERASI]. Sila pilih menu yang disediakan untuk urusan berkaitan. Jika ada sebarang kesulitan, sila hubungi kami di pejabat [NAMA KOPERASI].</p>
                                        <p>Sekian, terima kasih.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;<br>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <font class="maroonText">Maklumat Terkini</font>
                                        <hr size="1px">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Permohonan anggota baru: <font class="redText">1</font> permohonan<br>Pembiayaan baru:
                                        <font class="redText">0</font> pembiayaan
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;<br>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <font class="maroonText">Buletin [NAMA KOPERASI]</font>
                                        <hr size="1px">
                                    </td>
                                </tr>
                                <tr valign="top" class="textFont">
                                    <td>
                                        <table width="100%">
                                            <tr>
                                                <td><a href="/ikoop/[NAMA KOPERASI]/index.php?page=list&page_id=1&rec_per_page=50&sort=DESC">Arkib Buletin</a></td>
                                                <td align="right">Paparan&nbsp;
                                                    <select name="rec_per_page" onchange="PageRefresh();">
                                                        <option value="5">5</option>
                                                        <option value="10" selected="selected">10</option>
                                                        <option value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="40">40</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                    &nbsp;setiap mukasurat.&nbsp;
                                                    <select name="sort" onchange="PageRefresh();">
                                                        <option value="DESC" selected="selected">DESC</option>
                                                        <option value="ASC">ASC</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td valign="top">
                                        <table class="table table-sm m-3">
                                            <tr class="table-success">
                                                <td nowrap align="right" width="15">&nbsp;</td>
                                                <td nowrap align="left" width="">Perkara</td>
                                                <td nowrap align="center" width="10%">Tarikh</td>
                                                <td nowrap align="center" width="10%">Oleh</td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="top" class="Data" nowrap="nowrap">1</td>
                                                <td align="left" valign="top" class="Data" nowrap="nowrap">
                                                    <a href="/ikoop/[NAMA KOPERASI]/index.php?page=view&id=8">Syarat-syarat Pembiayaan</a>
                                                </td>
                                                <td align="center" valign="top" class="Data" nowrap="nowrap">05/04/2006</td>
                                                <td align="center" valign="top" class="Data">admin</td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="top" class="Data" nowrap="nowrap">2</td>
                                                <td align="left" valign="top" class="Data" nowrap="nowrap">
                                                    <a href="/ikoop/[NAMA KOPERASI]/index.php?page=view&id=7">Mempunyai Masalah?</a>
                                                </td>
                                                <td align="center" valign="top" class="Data" nowrap="nowrap">05/04/2006</td>
                                                <td align="center" valign="top" class="Data">admin</td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="top" class="Data" nowrap="nowrap">3</td>
                                                <td align="left" valign="top" class="Data" nowrap="nowrap">
                                                    <a href="/ikoop/[NAMA KOPERASI]/index.php?page=view&id=1">Selamat Datang!</a>
                                                </td>
                                                <td align="center" valign="top" class="Data" nowrap="nowrap">04/04/2006</td>
                                                <td align="center" valign="top" class="Data">admin</td>
                                            </tr>
                                        </table>

                                    </td>

                                </tr>
                                <tr>
                                    <td align="left" colspan="4">
                                        <font class="redText">1-3</font>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="textFont">Jumlah Rekod : <font class="redText">3</font>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <script language="JavaScript">
                        function PageRefresh() {
                            frm = document.MyForm;
                            document.location = "/ikoop/[NAMA KOPERASI]/index.php?page=main&page_id=1&rec_per_page=" + frm.rec_per_page.options[frm.rec_per_page.selectedIndex].value + "&sort=" + frm.sort.options[frm.sort.selectedIndex].value;
                        }
                    </script>
                </td>
            </tr>
            <tr>
                <td colspan="3" height="8" width="100%" style="background-image: url(images/horizontal.gif); background-repeat: repeat-x;"></td>
            </tr>
        </tbody>
    </table>


</div>