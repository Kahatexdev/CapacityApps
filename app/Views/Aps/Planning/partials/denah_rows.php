<?php
// partial: hanya menghasilkan <tr>...</tr> rows
// dibutuhkan: $layout (array)

$leftCap = 17;
$leftMaxRows = 3;
$centerCap = 15;
$centerMaxRows = 13;
$rightCap = 13;
$rightMaxRows = 13;
$totalRows = max($centerMaxRows, $rightMaxRows);

$normUpper = function ($s) {
    $s = (string)$s;
    $s = trim($s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};
$makeKey = function ($cell) use ($normUpper) {
    $no = (int)($cell['no_mc'] ?? 0);
    $jar = $normUpper($cell['jarum'] ?? '');
    return "{$no}-{$jar}";
};

$colspanMapRaw = [
    '159-TJ144' => 2,
    '1-TJ120' => 2,
    '3-TJ120' => 2,
    '17-TJ120' => 2,
    '160-TJ144' => 2,
    '18-TJ120' => 2,
    '3-TJ144' => 2,
    '2-TJ144' => 2,
    '1-TJ144' => 2,
    '22-TJ144' => 2,
    '23-TJ144' => 2,
    '24-TJ144' => 2,
    '27-TJ144' => 2,
    '26-TJ144' => 2,
    '25-TJ144' => 2,
    '78-TJ144' => 2,
    '79-TJ144' => 2,
    '81-TJ144' => 2,
    '80-TJ144' => 2,
    '90-JC168' => 2
];
$colspanMap = [];
foreach ($colspanMapRaw as $k => $v) {
    $p = explode('-', $k, 2);
    if (count($p) === 2) {
        [$no, $jar] = $p;
        $no = (int)$no;
        $jar = strtoupper(trim($jar));
        $colspanMap["{$no}-{$jar}"] = (int)$v;
    }
}
$getSpan = function ($cell) use ($makeKey, $colspanMap) {
    $k = $makeKey($cell);
    return max(1, ($colspanMap[$k] ?? 1));
};

$leftRows = array_fill(0, $leftMaxRows, []);
$centerRows = array_fill(0, $totalRows, []);
$rightRows  = array_fill(0, $totalRows, []);

$items = array_values($layout);
for ($r = 0; $r < $totalRows && !empty($items); $r++) {
    if ($r < $leftMaxRows) {
        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $leftCap) $span = $leftCap;
            if ($used + $span <= $leftCap) {
                $leftRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }

    $used = 0;
    while (!empty($items)) {
        $span = $getSpan($items[0]);
        if ($span > $centerCap) $span = $centerCap;
        if ($used + $span <= $centerCap) {
            $centerRows[$r][] = ['data' => array_shift($items), 'span' => $span];
            $used += $span;
        } else break;
    }

    $used = 0;
    while (!empty($items)) {
        $span = $getSpan($items[0]);
        if ($span > $rightCap) $span = $rightCap;
        if ($used + $span <= $rightCap) {
            $rightRows[$r][] = ['data' => array_shift($items), 'span' => $span];
            $used += $span;
        } else break;
    }
}

// helper renderCell
$renderCellHtml = function ($item) {
    $c = $item['data'];
    $spn = (int)($item['span'] ?? 1);
    $cls = 'gray-cell';
    if (isset($c['status'])) {
        if ($c['status'] === 'running') {
            $cls = 'bg-success text-white';
        } elseif ($c['status'] === 'idle') {
            $cls = 'bg-info text-white';
        } elseif ($c['status'] === 'sample') {
            $cls = 'bg-warning text-white';
        } elseif ($c['status'] === 'breakdown') {
            $cls = 'bg-danger text-white';
        }
    }

    $no = esc($c['no_mc'] ?? '-');
    $jar = esc($c['jarum'] ?? '-');
    $master = esc($c['mastermodel'] ?? '-');
    $inisial = esc($c['inisial'] ?? '-');
    $idprod = esc($c['id_produksi'] ?? '-');
    $idaps = esc($c['idapsperstyle'] ?? '-');

    $colspanAttr = $spn > 1 ? 'colspan="' . esc($spn) . '"' : '';

    // return string
    return "<td class=\"p-1 text-center\" {$colspanAttr}>
                <button class=\"cell {$cls}\" data-idprod=\"{$idprod}\" data-idaps=\"{$idaps}\">
                    <div class=\"m-no\">{$no}</div>
                    <div class=\"m-code\">{$jar}</div>
                    <div class=\"m-mastermodel\">{$master}</div>
                    <div class=\"m-inisial\">{$inisial}</div>
                </button>
            </td>";
};

// render rows → echo only <tr>...</tr>
for ($r = 0; $r < $totalRows; $r++) {
    echo '<tr>';

    if ($r < $leftMaxRows) {
        $used = 0;
        foreach ($leftRows[$r] as $it) {
            echo $renderCellHtml($it);
            $used += (int)($it['span'] ?? 1);
        }
        if ($used < $leftCap) {
            echo '<td class="left-pad" colspan="' . esc($leftCap - $used) . '"></td>';
        }
    } elseif ($r === $leftMaxRows) {
        $rowspanLeft = $totalRows - $leftMaxRows;
        echo '<td class="left-pad" colspan="' . esc($leftCap) . '" rowspan="' . esc($rowspanLeft) . '"></td>';
    }

    if ($r === 0) {
        echo '<td class="empty-cell" rowspan="' . esc($totalRows) . '"></td>';
    }

    // CENTER
    $used = 0;
    foreach ($centerRows[$r] as $it) {
        echo $renderCellHtml($it);
        $used += (int)($it['span'] ?? 1);
    }
    if ($used < $centerCap) {
        echo '<td class="left-pad" colspan="' . esc($centerCap - $used) . '"></td>';
    }

    if ($r === 0) {
        echo '<td class="empty-cell" rowspan="' . esc($totalRows) . '"></td>';
    }

    // RIGHT
    $used = 0;
    foreach ($rightRows[$r] as $it) {
        echo $renderCellHtml($it);
        $used += (int)($it['span'] ?? 1);
    }
    if ($used < $rightCap) {
        echo '<td class="left-pad" colspan="' . esc($rightCap - $used) . '"></td>';
    }

    echo '</tr>';
}
