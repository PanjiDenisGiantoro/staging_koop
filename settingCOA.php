<?php
/*********************************************************************************
 *  Filename  : settingCOA.php
 *  Modul     : Pengaturan COA Ledger Akuntansi
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2');
if (!$isAdmin) {
    print '<script>alert("Akses tidak diizinkan.");history.back();</script>';
    exit;
}

// --- Simpan ---
if ($action == 'Simpan') {
    $by  = get_session("Cookie_userName");
    $now = date("Y-m-d H:i:s");
    $ids = isset($_POST['settingID']) ? $_POST['settingID'] : array();
    foreach ($ids as $sid) {
        $sid  = intval($sid);
        $code = isset($_POST['ledger_code_' . $sid]) ? trim($_POST['ledger_code_' . $sid]) : '';
        $name = isset($_POST['ledger_name_' . $sid]) ? trim($_POST['ledger_name_' . $sid]) : '';
        $conn->Execute("UPDATE setting_coa SET ledger_code=" . tosql($code,"Text") . ", ledger_name=" . tosql($name,"Text") . ", updatedDate='$now', updatedBy=" . tosql($by,"Text") . " WHERE settingID=$sid");
    }
    activityLog('', 'Simpan Setting COA', get_session('Cookie_userID'), $by, 3);
    print '<script>alert("Setting COA berhasil disimpan.");window.location="?vw=settingCOA&mn=' . $mn . '";</script>';
    exit;
}

// --- Load semua setting, index by modul+kode_setting ---
$rsAll = $conn->Execute("SELECT * FROM setting_coa ORDER BY modul, settingID");
$coa   = array(); // $coa['pendanaan']['KAS_POOL'] = row
$allIDs = array();
while ($rsAll && !$rsAll->EOF) {
    $m = $rsAll->fields('modul');
    $k = $rsAll->fields('kode_setting');
    $coa[$m][$k] = array(
        'settingID'   => $rsAll->fields('settingID'),
        'label'       => $rsAll->fields('label'),
        'ledger_code' => $rsAll->fields('ledger_code'),
        'ledger_name' => $rsAll->fields('ledger_name'),
        'updatedDate' => $rsAll->fields('updatedDate'),
        'updatedBy'   => $rsAll->fields('updatedBy'),
    );
    $allIDs[] = $rsAll->fields('settingID');
    $rsAll->MoveNext();
}

// Peta jurnal per transaksi (debit/kredit)
$jurnal_map = array(
    'pendanaan' => array(
        array(
            'label'  => 'Tambah Pool Dana',
            'icon'   => 'mdi-bank-plus',
            'jenis'  => 'TAMBAH_POOL',
            'lines'  => array(
                array('posisi'=>'debit',  'role'=>'Dana masuk ke kas pool', 'kode'=>'KAS_POOL'),
                array('posisi'=>'kredit', 'role'=>'Sumber modal / dana',    'kode'=>'MODAL_DANA'),
            ),
        ),
        array(
            'label'  => 'Distribusi Dana',
            'icon'   => 'mdi-bank-transfer-out',
            'jenis'  => 'DISTRIBUSI',
            'lines'  => array(
                array('posisi'=>'debit',  'role'=>'Piutang ke peminjam',      'kode'=>'PIUTANG'),
                array('posisi'=>'kredit', 'role'=>'Kas pool berkurang',        'kode'=>'KAS_POOL'),
            ),
        ),
        array(
            'label'  => 'Bayar Cicilan',
            'icon'   => 'mdi-cash-check',
            'jenis'  => 'BAYAR_CICILAN',
            'lines'  => array(
                array('posisi'=>'debit',  'role'=>'Kas pool bertambah (pokok)', 'kode'=>'KAS_POOL'),
                array('posisi'=>'debit',  'role'=>'Pendapatan bunga',           'kode'=>'PENDAPATAN_BUNGA'),
                array('posisi'=>'kredit', 'role'=>'Piutang berkurang',          'kode'=>'PIUTANG'),
            ),
        ),
        array(
            'label'  => 'Bayar Denda',
            'icon'   => 'mdi-cash-remove',
            'jenis'  => 'BAYAR_DENDA',
            'lines'  => array(
                array('posisi'=>'debit',  'role'=>'Kas pool bertambah (pokok)', 'kode'=>'KAS_POOL'),
                array('posisi'=>'debit',  'role'=>'Pendapatan bunga',           'kode'=>'PENDAPATAN_BUNGA'),
                array('posisi'=>'debit',  'role'=>'Pendapatan denda keterlambatan', 'kode'=>'PENDAPATAN_DENDA'),
                array('posisi'=>'kredit', 'role'=>'Piutang berkurang',          'kode'=>'PIUTANG'),
            ),
        ),
    ),
    'pos' => array(
        array(
            'label' => 'Penjualan POS',
            'icon'  => 'mdi-cart-check',
            'jenis' => 'PENJUALAN',
            'lines' => array(
                array('posisi'=>'debit',  'role'=>'Kas / bank menerima pembayaran', 'kode'=>'KAS_PENJUALAN'),
                array('posisi'=>'kredit', 'role'=>'Pendapatan penjualan',           'kode'=>'PENDAPATAN_JUAL'),
            ),
        ),
        array(
            'label' => 'Harga Pokok Penjualan',
            'icon'  => 'mdi-package-variant',
            'jenis' => 'HPP',
            'lines' => array(
                array('posisi'=>'debit',  'role'=>'Beban HPP diakui', 'kode'=>'HPP'),
                array('posisi'=>'kredit', 'role'=>'Persediaan barang berkurang', 'kode'=>'PERSEDIAAN'),
            ),
        ),
    ),
    'produk' => array(
        array(
            'label' => 'Stok Masuk (Pembelian)',
            'icon'  => 'mdi-package-variant-plus',
            'jenis' => 'STOK_MASUK',
            'lines' => array(
                array('posisi'=>'debit',  'role'=>'Persediaan bertambah',   'kode'=>'PERSEDIAAN'),
                array('posisi'=>'kredit', 'role'=>'Beban / kas pembelian',  'kode'=>'BEBAN_PEMBELIAN'),
            ),
        ),
    ),
);

$modulLabel = array(
    'pendanaan' => 'Pendanaan Usaha',
    'pos'       => 'POS / Penjualan',
    'produk'    => 'Produk / Stok',
);
$modulColor = array(
    'pendanaan' => '#1a3c5e',
    'pos'       => '#1a5e3c',
    'produk'    => '#5e3c1a',
);
?>

<div class="maroon" align="left"><b>PENGATURAN COA LEDGER AKUNTANSI</b></div>
<div>&nbsp;</div>

<div class="alert alert-info py-2 mb-3" style="font-size:12px">
    <i class="mdi mdi-information-outline"></i>
    Setiap transaksi memerlukan <b>akun Debit</b> dan <b>akun Kredit (Pembanding)</b> sesuai kaidah pembukuan berpasangan.
    Klik <b>Pilih</b> untuk memilih akun dari daftar ledger. Jika kosong, sistem pakai kode default.
</div>

<form name="MyForm" action="?vw=settingCOA&mn=<?= $mn ?>" method="post">

<?php
// Output semua hidden settingID
foreach ($allIDs as $sid):
?>
<input type="hidden" name="settingID[]" value="<?= $sid ?>">
<?php endforeach; ?>

<?php foreach ($jurnal_map as $modul => $transaksiList):
    $coaMod = isset($coa[$modul]) ? $coa[$modul] : array();
?>
<div class="card mb-4">
    <div class="card-header py-2" style="background:<?= isset($modulColor[$modul]) ? $modulColor[$modul] : '#2b3a4a' ?>;color:#fff">
        <b><i class="mdi mdi-bank-outline"></i>
        <?= isset($modulLabel[$modul]) ? $modulLabel[$modul] : strtoupper($modul) ?></b>
    </div>
    <div class="card-body pb-2 pt-3 px-3">

    <?php foreach ($transaksiList as $trx): ?>
    <div class="mb-3">
        <div class="fw-bold mb-2" style="font-size:13px;color:#333">
            <i class="mdi <?= $trx['icon'] ?>"></i>
            <?= htmlspecialchars($trx['label']) ?>
            <small class="text-muted ms-2" style="font-size:10px;font-weight:normal">[<?= $trx['jenis'] ?>]</small>
        </div>
        <table class="table table-sm table-bordered mb-0" style="font-size:12px">
            <thead>
                <tr style="background:#f8f9fa">
                    <th width="9%"  class="text-center">Posisi</th>
                    <th width="22%">Keterangan Baris Jurnal</th>
                    <th width="14%">Kode Akun</th>
                    <th>Nama Akun</th>
                    <th width="10%" class="text-center">Aksi</th>
                    <th width="13%">Diperbarui</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($trx['lines'] as $line):
                $kode = $line['kode'];
                $row  = isset($coaMod[$kode]) ? $coaMod[$kode] : array('settingID'=>'','ledger_code'=>'','ledger_name'=>'','updatedDate'=>'','updatedBy'=>'');
                $sid  = $row['settingID'];
                $isDebit = $line['posisi'] === 'debit';
            ?>
            <tr>
                <td class="text-center">
                    <?php if ($isDebit): ?>
                    <span class="badge" style="background:#1a5e3c;font-size:11px">Debit</span>
                    <?php else: ?>
                    <span class="badge" style="background:#5e1a1a;font-size:11px">Kredit</span>
                    <?php endif; ?>
                </td>
                <td style="color:#555"><?= htmlspecialchars($line['role']) ?></td>
                <td>
                    <?php if ($sid): ?>
                    <input type="text"
                           name="ledger_code_<?= $sid ?>"
                           id="lc_<?= $sid ?>"
                           class="form-control form-control-sm"
                           value="<?= htmlspecialchars($row['ledger_code']) ?>"
                           readonly style="font-family:monospace;font-size:12px">
                    <?php else: ?>
                    <span class="text-muted" style="font-size:11px">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($sid): ?>
                    <input type="text"
                           name="ledger_name_<?= $sid ?>"
                           id="ln_<?= $sid ?>"
                           class="form-control form-control-sm"
                           value="<?= htmlspecialchars($row['ledger_name']) ?>"
                           readonly style="font-size:12px">
                    <?php else: ?>
                    <span class="text-muted" style="font-size:11px">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if ($sid): ?>
                    <button type="button" class="btn btn-xs btn-info py-0 px-2"
                            onclick="piliLedger(<?= $sid ?>)">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                    <?php if ($row['ledger_code']): ?>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1"
                            onclick="clearLedger(<?= $sid ?>)" title="Hapus">&times;</button>
                    <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td style="font-size:10px;color:#999">
                    <?= (isset($row['updatedBy']) && $row['updatedBy']) ? htmlspecialchars($row['updatedBy']) . '<br>' . toDate("d/m/Y H:i", $row['updatedDate']) : '-' ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Visual check: debit = kredit -->
        <?php
        $dLines = array(); $kLines = array();
        foreach ($trx['lines'] as $_l) { if ($_l['posisi']==='debit') $dLines[] = $_l; else $kLines[] = $_l; }
        $allSet = true;
        foreach ($trx['lines'] as $l) {
            $k = $l['kode'];
            if (!isset($coaMod[$k]) || !$coaMod[$k]['ledger_code']) { $allSet = false; break; }
        }
        ?>
        <div class="mt-1" style="font-size:10.5px;color:#888">
            <i class="mdi mdi-arrow-right-bold-circle-outline"></i>
            Debit: <b><?= count($dLines) ?> akun</b> &nbsp;|&nbsp;
            Kredit: <b><?= count($kLines) ?> akun</b>
            <?php if ($allSet): ?>
            &nbsp;<span class="badge bg-success" style="font-size:10px">Lengkap</span>
            <?php else: ?>
            &nbsp;<span class="badge bg-warning text-dark" style="font-size:10px">Belum semua diisi</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    </div>
</div>
<?php endforeach; ?>

<div class="mt-1 mb-4">
    <input type="submit" name="action" value="Simpan" class="btn btn-primary">
    &nbsp;
    <a href="?vw=settingCOA&mn=<?= $mn ?>" class="btn btn-outline-secondary">Refresh</a>
</div>
</form>

<script>
function piliLedger(sid) {
    window.open(
        'listledger.php?targetCode=lc_' + sid + '&targetName=ln_' + sid,
        'selCOA', 'top=50,left=100,width=900,height=600,scrollbars=yes,resizable=yes'
    );
}
function clearLedger(sid) {
    document.getElementById('lc_' + sid).value = '';
    document.getElementById('ln_' + sid).value = '';
}
</script>
<style>
.btn-xs { padding: 1px 6px; font-size: 11px; }
</style>
