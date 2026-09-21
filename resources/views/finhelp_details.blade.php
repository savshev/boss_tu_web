<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 850px; height: 85vh; display: flex; flex-direction: column; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        .details-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; position: relative; }

        .detail-item {
            background: #f8f9fa;
            border-left: 4px solid #ced4da;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace, sans-serif;
            cursor: pointer;
            transition: background-color 0.15s ease;
            outline: none;
            display: grid;
            grid-template-columns: 90px 220px 1fr 130px;
            gap: 12px;
            align-items: center;
            font-size: 14px;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .col-tabnom { text-align: right; font-weight: bold; color: #007bff; }
        .col-fam { text-align: left; font-weight: bold; color: #111; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .col-info { text-align: left; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .col-summa { text-align: right; font-weight: bold; color: #222; }

        .sum-val { color: #222; font-weight: bold; }

        /* Модальное окно */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #ffffff; padding: 25px; border-radius: 10px; width: 90%; max-width: 700px; max-height: 80vh; display: flex; flex-direction: column; box-shadow: 0 5px 20px rgba(0,0,0,0.3); }
        .modal-header { font-size: 17px; font-weight: bold; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .btn-close-modal { padding: 6px 12px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-close-modal:hover { background-color: #5a6268; }
        .modal-body { flex: 1; overflow-y: auto; }

        .modal-table { width: 100%; border-collapse: collapse; font-family: 'Courier New', monospace, sans-serif; font-size: 14px; }
        .modal-table th, .modal-table td { padding: 8px 10px; border-bottom: 1px solid #dee2e6; text-align: left; }
        .modal-table th { background-color: #f8f9fa; font-weight: bold; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="details-list" id="detailsList" tabindex="0">
        @forelse($records as $index =>$row)
            @php
                $arr = (array)$row;
                $partnerVal = trim((string)($arr['PARTNER'] ?? $arr['partner'] ?? ''));
                $tabNomRaw  = (int) ($arr['TAB_NOM'] ?? $arr['tab_nom'] ?? 0);$fam        = trim((string)($arr['FAM_RUS'] ?? $arr['fam_rus'] ?? ''));
                $otch       = trim((string)($arr['OTCH_RUS'] ?? $arr['otch_rus'] ?? ''));$ima        = trim((string)($arr['IMA_RUS'] ?? $arr['ima_rus'] ?? ''));
                $finhInfo   = trim((string)($arr['FINH_INFO'] ?? $arr['finh_info'] ?? ''));$summaVal   = (float) ($arr['SUMMA'] ?? $arr['summa'] ?? 0);

                $fullFio    = trim(preg_replace('/\s+/', ' ', "{$fam} {$otch} {$ima}"));
                $tabDisplay = $tabNomRaw === 0 ? 'Ветеран' : str_pad($tabNomRaw, 8, ' ', STR_PAD_LEFT);
                $fmtSumma   = number_format($summaVal, 2, '.', '');
            @endphp
            <div class="detail-item {{ $index === 0 ? 'active' : '' }}"
                 data-partner="{{ $partnerVal }}"
                 data-fio="{{ $fullFio }}"
                 tabindex="0">

                <div class="col-tabnom">{{ $tabDisplay }}</div>
                <div class="col-fam">{{ $fullFio !== '' ?$fullFio : 'ПІБ не вказано' }}</div>
                <div class="col-info">{{ $finhInfo !== '' ?$finhInfo : '—' }}</div>
                <div class="col-summa"><span class="sum-val">{{ $fmtSumma }}</span> грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи допомоги у таблиці SQL_FINH за {{ $year }} рік відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('finhelp') }}" class="btn-back">← Назад до років</a>
    </div>
</div>

<!-- Модальное окно истории материальной помощи сотрудника -->
<div class="modal-overlay" id="finhelpModal">
    <div class="modal-content">
        <div class="modal-header">
            <span id="modalTitle">Усі матеріальні допомоги працівника (SQL_FINH)</span>
            <button class="btn-close-modal" onclick="closeFinhelpModal()">✕ Закрити</button>
        </div>
        <div class="modal-body">
            <table class="modal-table">
                <thead>
                <tr>
                    <th style="width: 110px;">Дата</th>
                    <th>Інформація (FINH_INFO)</th>
                    <th style="width: 130px; text-align: right;">Сума</th>
                </tr>
                </thead>
                <tbody id="modalTableBody">
                <!-- Динамически заполняется через JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let items = Array.from(document.querySelectorAll('.detail-item'));
        if (items.length === 0) return;

        let currentIndex = 0;

        function openFinhelpModalForItem(item) {
            const partner = item.getAttribute('data-partner');
            const fio = item.getAttribute('data-fio') || 'Працівника';

            if (!partner) return;

            document.getElementById('modalTitle').textContent = `Матеріальна допомога: ${fio}`;
            document.getElementById('modalTableBody').innerHTML = '<tr><td colspan="3" style="text-align:center;">Завантаження з SQL_FINH...</td></tr>';
            document.getElementById('finhelpModal').style.display = 'flex';

            fetch(`/finhelp/person-details/${partner}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('modalTableBody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; color:#dc3545;">Записи у таблиці SQL_FINH відсутні</td></tr>';
                        return;
                    }

                    data.forEach(row => {
                        const dateVal = row.DATE ? new Date(row.DATE).toLocaleDateString('uk-UA') : '—';
                        const infoVal = row.FINH_INFO ? row.FINH_INFO.trim() : '—';
                        const sumVal = parseFloat(row.SUMMA || 0).toFixed(2);

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                                <td>${dateVal}</td>
                                <td>${infoVal !== '' ? infoVal : '—'}</td>
                                <td style="text-align: right; font-weight: bold;">${sumVal} грн</td>
                            `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    document.getElementById('modalTableBody').innerHTML = '<tr><td colspan="3" style="text-align:center; color:#dc3545;">Помилка завантаження даних</td></tr>';
                });
        }

        function setActiveItem(index) {
            items.forEach(item => item.classList.remove('active'));

            if (index < 0) index = 0;
            if (index >= items.length) index = items.length - 1;

            currentIndex = index;
            const activeItem = items[currentIndex];
            activeItem.classList.add('active');

            activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        items.forEach((item, index) => {
            item.addEventListener('click', function () {
                setActiveItem(index);
            });

            item.addEventListener('dblclick', function () {
                openFinhelpModalForItem(item);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (document.getElementById('finhelpModal').style.display === 'flex') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < items.length - 1) setActiveItem(currentIndex + 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) setActiveItem(currentIndex - 1);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items[currentIndex]) openFinhelpModalForItem(items[currentIndex]);
            }
        });
    });

    function closeFinhelpModal() {
        document.getElementById('finhelpModal').style.display = 'none';
    }
</script>
</body>
</html>
