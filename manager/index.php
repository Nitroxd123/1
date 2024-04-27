<?
require_once ("../library/CheckChecker.php"); 
require_once ("../library/SimpleXLSX.php"); 
use Shuchkin\SimpleXLSX;

$allItems = CheckChecker::getAllChecks();

$filePath = '../library/files/table.xlsx';
$settingsPath = '../library/files/table-settings.txt';

if (file_exists($filePath) && file_exists($settingsPath)) {
	$xlsx = SimpleXLSX::parse($filePath);

	$xlsxAllItems = array();
	$activeCount = 0;


	$fileJson = file_get_contents($settingsPath);
    $arSettings = json_decode($fileJson, true);

	if( count($arSettings) === 3 ){
		foreach( $xlsx->rows() as $rowNum => $r ){
			if( in_array($rowNum, array(0, 1)) ) continue;
		
			$xlsxAllItems[$rowNum]['ID'] = $r[$arSettings['COL_ID']];
			$xlsxAllItems[$rowNum]['DATE'] = $r[$arSettings['COL_DATE']];
			$xlsxAllItems[$rowNum]['PRICE'] = $r[$arSettings['COL_PRICE']];
			$xlsxAllItems[$rowNum]['ACTIVE'] = false;
		
			$date = date('Ymd\THi', strtotime($r[6]));
			foreach( $allItems as $arItem ){
		
				if( $date === $arItem['DATE'] && floatval($arItem['PRICE']) === floatval($xlsxAllItems[$rowNum]['PRICE']) ){
		
					$xlsxAllItems[$rowNum]['ACTIVE'] = true;
					$activeCount++;
				}
			}
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Сверка с таблицей</title>
	<link rel="stylesheet" href="/assets/css/main.css">
	<link rel="stylesheet" href="/assets/css/modal.css">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
	<script src="/assets/js/main.js"></script>
</head>
<body>
	<a class="lk-link" href="/">Добавить чеки</a>
	<div class="wrapper">
		<button class="add-button" data-custom-open="modal-1" role="button">+</button>


		<div class="check-items__block">
			<div class="check-items__title">
				<? if( isset($xlsxAllItems) && count($xlsxAllItems) > 0 ): ?>
					Найдено чеков <?= count($allItems) ?>, из них совпадают с табицей <?= $activeCount ?> из <?= count($xlsxAllItems) ?>
				<? else: ?>
					Файл таблицы не найден
				<? endif ?>
			</div>
			<div class="check-items xlsx">

				<? if( isset($xlsxAllItems) && count($xlsxAllItems) > 0 ): ?>
					<? foreach( $xlsxAllItems as $rowNum => $xlsxItem ): ?>

						<div class="check-item<?= $xlsxItem['ACTIVE'] ? ' active' : '' ?>">
							<div class="check-left">
								<div class="check-type">ID: <?= $xlsxItem['ID'] ?> (строка <?= $rowNum ?>)</div>
								<div class="check-date"><?= date('Y-m-d H:i', strtotime($xlsxItem['DATE'])); ?></div>
							</div>
							<div class="check-price">Цена: <b><?= $xlsxItem['PRICE'] ?> руб</b></div>
						</div>

					<? endforeach ?>
				<? endif ?>

			</div>
		</div>

		<div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
			<div class="modal__overlay" tabindex="-1" data-micromodal-close>
				<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
					<header class="modal__header">
						<h2 class="modal__title" id="modal-1-title">
							<?= isset($xlsx) && $xlsx ? 'Заменить Таблицу' : 'Добавить Таблицу' ?>
						</h2>
						<button class="modal__close" aria-label="Close modal" data-custom-close></button>
					</header>
					<main class="modal__content" id="modal-1-content">
						<form class="add-form add-xlsx-form-js">
							<label class="file-label-js">
								<span>Файл c таблицей(xlsx)<i>*</i></span>
								<input name="xlsx" type="file" placeholder="123">
							</label>
							<div class="hidden-fields table-props-js" style="display: none;">
								<button type="submit">Добавить</button>
							</div>
						</form>
					</main>
				</div>
			</div>
		</div>
	</div>
</body>
</html>