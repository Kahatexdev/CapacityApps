<style>
    .empty-span {
        background: repeating-linear-gradient(90deg,
                rgba(148, 163, 184, .25),
                rgba(148, 163, 184, .25) 4px,
                transparent 4px,
                transparent 8px);
        padding: 0;
    }

    .left-pad {
        background: transparent;
    }

    .empty-cell {
        background: transparent;
        width: 6px;
    }
</style>

<?php
/* --- SETTINGS & OFFSETS --- */
$leftMaxRows     = $leftMaxRows     ?? 15;
$leftCapTop      = $leftCapTop      ?? 17; // cap baris 1..THRESHOLD_ROW
$leftCapAfter    = $leftCapAfter    ?? 13; // cap baris > THRESHOLD_ROW

$centerMaxRows   = $centerMaxRows   ?? 12;
$centerOffsetRows = $centerOffsetRows ?? 8;  // mulai render CENTER setelah offset baris ini (0-based)
$centerCapTop    = $centerCapTop    ?? 14;
$centerCapAfter  = $centerCapAfter  ?? 14;
$centerBottomRows   = 2;  // berapa baris terakhir yg kapasitasnya beda
$centerCapBottom    = 9;  // kapasitas kolom utk baris-baris itu


$rightMaxRows    = $rightMaxRows    ?? 12;
$rightOffsetRows = $rightOffsetRows ?? 8;
$rightCapTop     = $rightCapTop     ?? 15;
$rightCapAfter   = $rightCapAfter   ?? 15;

/* baris patokan (1-based). Setelah baris ini pakai cap After */
if (!defined('THRESHOLD_ROW')) define('THRESHOLD_ROW', 9);

/* totalRows harus cover offset + panjang efektif */
$totalRows = max(
    $leftMaxRows,
    $centerOffsetRows + $centerMaxRows,
    $rightOffsetRows  + $rightMaxRows
);

/* --- EMPTY BLOCK RULES (sesuaikan) ---
 * Row dihitung PER-AREA (1-based setelah offset untuk center/right).
 */
$emptyBlocks = $emptyBlocks ?? [
    // contoh:
    // ['area'=>'left','row'=>10,'span'=>5,'class'=>'empty-span'],
    // ['area' => 'center', 'row' => 2, 'span' => 4, 'class' => 'empty-span'],
    ['area' => 'center', 'row' => 2, 'span' => 4, 'at_col' => 0, 'class' => 'empty-span'],
    ['area' => 'center', 'row' => 3, 'span' => 4, 'at_col' => 0, 'class' => 'empty-span'],
    ['area' => 'center', 'row' => 4, 'span' => 4, 'at_col' => 0, 'class' => 'empty-span'],
    ['area' => 'center', 'row' => 5, 'span' => 4, 'at_col' => 0, 'class' => 'empty-span'],
    // ['area' => 'right', 'row' => 3, 'span' => 6, 'at_col' => 0, 'class' => 'empty-span'],
    // ['area' => 'center', 'row' => 4, 'span' => 4, 'at_col' => 0, 'class' => 'empty-span'],
    // ['area' => 'left', 'row' => 4, 'span' => 5, 'at_col' => 5, 'class' => 'empty-span'],
];

/* --- UTIL BANTU --- */
$normUpper = function ($s) {
    $s = (string)$s;
    $s = trim($s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};
$makeKey = function ($cell) use ($normUpper) {
    $idMC = (int)($cell['id'] ?? 0);
    return "{$idMC}";
};

/* Mesin tertentu bisa colspan > 1 */
$colspanMapRaw = $colspanMapRaw ?? [
    // contoh: '25,26,27' => 2
    // '885,923,961,994,,995' => 4,
    '962,995' => 6,
];
$colspanMap = [];
foreach ($colspanMapRaw as $key => $val) {
    foreach (preg_split('/\s*,\s*/', (string)$key) as $idStr) {
        if ($idStr === '') continue;
        $idMC = (int)$idStr;
        if ($idMC > 0) $colspanMap[(string)$idMC] = (int)$val;
    }
}
$getSpan = function ($cell) use ($makeKey, $colspanMap) {
    $k = $makeKey($cell);
    return max(1, ($colspanMap[$k] ?? 1));
};

/* cap dinamis per section & baris (r1 = 1-based “global”) */
$rowCap = function (string $section, int $r1) use (
    $leftCapTop,
    $leftCapAfter,
    $centerCapTop,
    $centerCapAfter,
    $rightCapTop,
    $rightCapAfter,
    $centerOffsetRows,   // ⬅️ tambahkan
    $centerMaxRows,      // ⬅️ tambahkan
    $centerBottomRows,   // ⬅️ tambahkan
    $centerCapBottom     // ⬅️ tambahkan
) {
    $after = ($r1 > THRESHOLD_ROW);

    if ($section === 'left') {
        return $after ? $leftCapAfter : $leftCapTop;
    }

    if ($section === 'center') {
        // hitung baris relatif center (1-based) terhadap offset
        $rel = $r1 - $centerOffsetRows; // bisa <1 kalau masih di atas offset
        // jika berada di 2 baris terakhir center, pakai cap 9
        if ($rel >= 1 && $rel >= ($centerMaxRows - $centerBottomRows + 1) && $rel <= $centerMaxRows) {
            return $centerCapBottom; // 9 kolom
        }
        // selain itu ikut aturan biasa (top/after)
        return $after ? $centerCapAfter : $centerCapTop;
    }

    if ($section === 'right') {
        return $after ? $rightCapAfter : $rightCapTop;
    }

    return 0;
};


/* --- BUFFER ROWS --- */
$leftRows   = array_fill(0, $leftMaxRows, []);
$centerRows = array_fill(0, $totalRows, []); // simpan di indeks global r (0..totalRows-1)
$rightRows  = array_fill(0, $totalRows, []);

/* --- INDEX emptyBlocks agar cepat diakses per-area/per-baris --- */
$emptyIdx = ['left' => [], 'center' => [], 'right' => []];
foreach ($emptyBlocks as $blk) {
    $a    = strtolower(trim((string)($blk['area'] ?? '')));
    $row  = (int)($blk['row'] ?? 0);      // 1-based per-area
    $span = max(1, (int)($blk['span'] ?? 1));
    $cls  = trim((string)($blk['class'] ?? 'empty-span'));
    if (!in_array($a, ['left', 'center', 'right'], true) || $row <= 0) continue;

    // ⬇️ tambahkan at_col (opsional; 0 = prepend di paling kiri area)
    $at   = max(0, (int)($blk['at_col'] ?? 0));

    $emptyIdx[$a][$row] = ['span' => $span, 'class' => $cls, 'at_col' => $at];
}

/* --- DISTRIBUSI ITEM (packing) --- */
$items = array_values(is_array($layout) ? $layout : []);

for ($r = 0; $r < $totalRows; $r++) {
    $r1 = $r + 1;

    // LEFT (selalu dari r=0 s/d leftMaxRows-1)
    if ($r < $leftMaxRows) {
        $capL = $rowCap('left', $r1);
        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $capL) $span = $capL;
            if ($used + $span <= $capL) {
                $leftRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }

    // CENTER (mulai setelah offset)
    // CENTER (mulai setelah offset)
    // CENTER (mulai setelah offset)
    if ($r >= $centerOffsetRows && ($r - $centerOffsetRows) < $centerMaxRows) {
        $capC = $rowCap('center', $r1);

        // row relatif untuk center (1-based)
        $centerRowRel = $r - $centerOffsetRows + 1;

        // ⬇️ paksa 2 baris terakhir center = 9 kolom
        if ($centerRowRel >= ($centerMaxRows - $centerBottomRows + 1)) {
            $capC = $centerCapBottom; // 9
        }

        // reserve gap di baris ini
        $reservedGap = 0;
        if (!empty($emptyIdx['center'][$centerRowRel]['span'])) {
            $reservedGap = min($capC, (int)$emptyIdx['center'][$centerRowRel]['span']);
        }

        $capEff = max(0, $capC - $reservedGap);
        $used   = 0;

        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $capEff) $span = $capEff;
            if ($used + $span <= $capEff) {
                $centerRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }



    // RIGHT (mulai setelah offset)
    if ($r >= $rightOffsetRows && ($r - $rightOffsetRows) < $rightMaxRows) {
        $capR = $rowCap('right', $r1);
        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $capR) $span = $capR;
            if ($used + $span <= $capR) {
                $rightRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }
}




/* --- RENDER HELPER --- */
$renderCellHtml = function ($item) {
    $c   = $item['data'];
    $spn = (int)($item['span'] ?? 1);

    $cls = 'gray-cell';
    if (isset($c['status'])) {
        if ($c['status'] === 'running')       $cls = 'bg-success text-white';
        elseif ($c['status'] === 'idle')      $cls = 'bg-info text-white';
        elseif ($c['status'] === 'sample')    $cls = 'bg-warning text-white';
        elseif ($c['status'] === 'breakdown') $cls = 'bg-danger text-white';
    }

    $no     = esc($c['no_mc'] ?? '-');
    $jar    = esc($c['jarum'] ?? '-');
    $master = esc($c['mastermodel'] ?? '-');
    $inis   = esc($c['inisial'] ?? '-');
    $idprod = esc($c['id_produksi'] ?? '-');
    $idaps  = esc($c['idapsperstyle'] ?? '-');

    $colspanAttr = $spn > 1 ? 'colspan="' . esc($spn) . '"' : '';

    return "<td class=\"p-1 text-center\" {$colspanAttr}>
            <button class=\"cell {$cls}\" data-idprod=\"{$idprod}\" data-idaps=\"{$idaps}\">
              <div class=\"m-no\">{$no}</div>
              <div class=\"m-code\">{$jar}</div>
              <div class=\"m-mastermodel\">{$master}</div>
              <div class=\"m-inisial\">{$inis}</div>
            </button>
          </td>";
};

/* --- RENDER BARIS --- */
for ($r = 0; $r < $totalRows; $r++) {
    $r1 = $r + 1;
    echo '<tr>';

    /* ---------- LEFT ---------- */
    $capL = $rowCap('left', $r1);
    $used = 0;
    if ($r < $leftMaxRows) {
        // sisipkan blok kosong LEFT jika ada rule pada baris ini
        $rule = $emptyIdx['left'][$r1] ?? null; // LEFT pakai row global 1..leftMaxRows
        if ($rule) {
            $ins = min($rule['span'], max(0, $capL - $used));
            if ($ins > 0) {
                echo '<td class="' . esc($rule['class']) . '" colspan="' . esc($ins) . '"></td>';
                $used += $ins;
            }
        }
        // render mesin
        foreach ($leftRows[$r] as $it) {
            // stop jika sisa slot habis
            $spanIt = (int)($it['span'] ?? 1);
            if ($used + $spanIt > $capL) break;
            echo $renderCellHtml($it);
            $used += $spanIt;
        }
    }
    if ($used < $capL) {
        echo '<td class="left-pad" colspan="' . esc($capL - $used) . '"></td>';
    }

    /* ---------- GUTTER kiri–tengah ---------- */
    if ($r === 0) echo '<td class="empty-cell" rowspan="' . esc($totalRows) . '"></td>';

    /* ---------- CENTER ---------- */
    /* ---------- CENTER (dengan at_col) ---------- */
    $capC = $rowCap('center', $r1);
    $used = 0;

    // baris relatif untuk area center (1-based, setelah offset)
    $centerRowRel = ($r >= $centerOffsetRows) ? ($r - $centerOffsetRows + 1) : 0;
    $centerActive = ($r >= $centerOffsetRows && $centerRowRel >= 1 && $centerRowRel <= $centerMaxRows);

    // ⬇️ paksa 2 baris terakhir center = 9 kolom saat render
    if ($centerActive && $centerRowRel >= ($centerMaxRows - $centerBottomRows + 1)) {
        $capC = $centerCapBottom; // 9
    }

    // rule untuk baris ini (jika ada)
    $rule  = ($centerRowRel >= 1 && $centerRowRel <= $centerMaxRows)
        ? ($emptyIdx['center'][$centerRowRel] ?? null)
        : null;

    $atCol = (int)($rule['at_col'] ?? 0);           // 0 = prepend (paling kiri area)
    $want  = (int)($rule['span']   ?? 0);
    $cls   = (string)($rule['class'] ?? 'empty-span');

    // hanya render mesin kalau baris center aktif
    $centerActive = ($r >= $centerOffsetRows && $centerRowRel >= 1 && $centerRowRel <= $centerMaxRows);

    // 1) render mesin sampai sebelum posisi at_col (jika at_col > 1)
    $startIdx = 0;
    if ($centerActive && $atCol > 1) {
        $cnt = count($centerRows[$r]);
        while ($startIdx < $cnt) {
            $spanIt = (int)($centerRows[$r][$startIdx]['span'] ?? 1);
            // Jika menambah mesin ini >= at_col, berhenti (biar gap muncul di sini)
            if ($used + $spanIt >= $atCol) break;

            echo $renderCellHtml($centerRows[$r][$startIdx]);
            $used += $spanIt;
            $startIdx++;
            if ($used >= $capC) break; // safeguard
        }
    }

    // 2) sisipkan gap (kalau ada rule)
    if ($want > 0) {
        $ins = min($want, max(0, $capC - $used)); // clamp ke sisa slot
        if ($ins > 0) {
            echo '<td class="' . esc($cls) . '" colspan="' . esc($ins) . '"></td>';
            $used += $ins;
        }
    }

    // 3) render sisa mesin setelah gap (atau dari awal kalau at_col=0)
    if ($centerActive) {
        $cnt = count($centerRows[$r]);
        for ($i = $startIdx; $i < $cnt; $i++) {
            $spanIt = (int)($centerRows[$r][$i]['span'] ?? 1);
            if ($used + $spanIt > $capC) break;
            echo $renderCellHtml($centerRows[$r][$i]);
            $used += $spanIt;
        }
    }


    // 4) padding sisa baris
    if ($used < $capC) {
        echo '<td class="left-pad" colspan="' . esc($capC - $used) . '"></td>';
    }



    /* ---------- GUTTER kanan–tengah ---------- */
    if ($r === 0) echo '<td class="empty-cell" rowspan="' . esc($totalRows) . '"></td>';

    /* ---------- RIGHT ---------- */
    $capR = $rowCap('right', $r1);
    $used = 0;

    // index baris RELATIF area right (1-based)
    $rightRowRel = ($r >= $rightOffsetRows) ? ($r - $rightOffsetRows + 1) : 0;

    // blok kosong right
    if ($rightRowRel >= 1 && $rightRowRel <= $rightMaxRows) {
        $rule = $emptyIdx['right'][$rightRowRel] ?? null;
        if ($rule) {
            $ins = min($rule['span'], max(0, $capR - $used));
            if ($ins > 0) {
                echo '<td class="' . esc($rule['class']) . '" colspan="' . esc($ins) . '"></td>';
                $used += $ins;
            }
        }
    }

    // render mesin right jika baris right aktif
    if ($r >= $rightOffsetRows && $rightRowRel >= 1 && $rightRowRel <= $rightMaxRows) {
        foreach ($rightRows[$r] as $it) {
            $spanIt = (int)($it['span'] ?? 1);
            if ($used + $spanIt > $capR) break;
            echo $renderCellHtml($it);
            $used += $spanIt;
        }
    }
    if ($used < $capR) {
        echo '<td class="left-pad" colspan="' . esc($capR - $used) . '"></td>';
    }

    echo '</tr>';
}
?>