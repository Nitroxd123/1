<?
require_once (__DIR__ . "/library/CheckChecker.php"); 
$allItems = CheckChecker::getAllChecks();

if(isset($_GET['type']) && $_GET['type']){
	$arFilter = array(
		'housing' => 'Проживание', 
		'food' => 'Питание',
		'transit' => 'Проезд',
	);

	$filterType = $arFilter[$_GET['type']];
}

if( isset($allItems) && count($allItems) > 0 ){

	$allCount = count($allItems);
	$housingCount = 0;
	$foodCount = 0;
	$transitCount = 0;
	$priceSum = 0;

	foreach( $allItems as $key => $arItem ){
		if($arItem['TYPE'] === 'Проживание') $housingCount++;
		if($arItem['TYPE'] === 'Питание') $foodCount++;
		if($arItem['TYPE'] === 'Проезд') $transitCount++;

		if( isset($filterType) && $arItem['TYPE'] !== $filterType ) unset($allItems[$key]);
	}

	foreach( $allItems as $arItem ) $priceSum += floatval($arItem['PRICE']);
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Загрузка чеков</title>
	<link rel="stylesheet" href="/assets/css/main.css">
	<link rel="stylesheet" href="/assets/css/modal.css">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
	<script src="/assets/js/moment.js"></script>
	<script src="/assets/js/main.js"></script>
</head>
<body>
	<a class="lk-link" href="/manager/">Сравнить в таблице</a>
	<div class="wrapper">
		<button class="add-button" data-custom-open="modal-1" role="button">+</button>


		<div class="check-items__block">
			<div class="check-items__title"><span>Ваши чеки</span><span>Сумма чеков: <?= number_format($priceSum, 2, ',', ' '); ?> руб</span></div>
			<div class="check-filter">
				<div class="check-filter-tabs">
					<a href="/"<?= !isset($_GET['type']) ? ' class="active"' : '' ?>>Все (<?= $allCount ?>)</a>
					<a href="/?type=transit"<?= isset($_GET['type']) && $_GET['type'] === 'transit' ? ' class="active"' : '' ?>>Проезд (<?= $transitCount ?>)</a>
					<a href="/?type=housing"<?= isset($_GET['type']) && $_GET['type'] === 'housing' ? ' class="active"' : '' ?>>Проживание (<?= $housingCount ?>)</a>
					<a href="/?type=food"<?= isset($_GET['type']) && $_GET['type'] === 'food' ? ' class="active"' : '' ?>>Питание (<?= $foodCount ?>)</a>
				</div>
				<?/*
				<select name="type" required>
					<option value="">Сортировка</option>
					<option value="create">По дате добавления чека</option>
					<option value="date">По дате</option>
					<option value="price">По цене</option>
				</select>
				*/?>
			</div>
			<div class="check-items">

				<? if( isset($allItems) && count($allItems) > 0 ): 
					foreach( $allItems as $allItem ): ?>

						<div class="check-item">
							<div class="check-left">

								<? if( isset($allItem['TYPE']) ): ?>
									<div class="check-type"><?= $allItem['TYPE'] ?><?= isset($allItem['TYPE_COMMENT']) && $allItem['TYPE_COMMENT'] ? ' (' . $allItem['TYPE_COMMENT'] . ')' : '' ?></div>
								<? endif ?>

								<div class="check-date"><?= date('Y-m-d H:i', strtotime($allItem['DATE'])); ?></div>
							</div>
							<div class="check-price">Цена: <b><?= $allItem['PRICE'] ?> руб</b></div>
						</div>

					<? endforeach;
				endif ?>

			</div>
		</div>

		<div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
			<div class="modal__overlay" tabindex="-1" data-micromodal-close>
				<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
					<header class="modal__header">
						<h2 class="modal__title" id="modal-1-title">
							Добавить чек
						</h2>
						<button class="modal__close" aria-label="Close modal" data-custom-close></button>
					</header>
					<main class="modal__content" id="modal-1-content">
						<div class="type-buttons type-buttons-js">
							<button data-id="1">Ввести вручную</button>
							<button data-id="2">Загрузить фото QR кода</button>
						</div>
						<form class="add-form add-check-form-js check-type-1-js" style="display: none;">
							<label>
								<span>Дата указанная в чеке<i>*</i></span>
								<input name="date" type="date" placeholder="" required>
							</label>
							<label>
								<span>Время указанное в чеке<i>*</i></span>
								<input name="time" type="time" required />
							</label>
							<label>
								<span>Цена указанная в чеке<i>*</i></span>
								<input name="price" type="text" placeholder="Цена" required>
							</label>
							<label>
								<span>Тип расхода<i>*</i></span>
								<select name="type" required>
									<option value="">Выберете тип чека</option>
									<option value="Проживание">Проживание</option>
									<option value="Питание">Питание</option>
									<option value="Проезд">Проезд</option>
								</select>
							</label>
							<label>
								<span>Тип расхода (пометка)</span>
								<input name="type_comment" type="text" placeholder="Тип расхода (пометка)">
							</label>
							<button type="submit">Добавить</button>
						</form>

						<form class="add-form add-check-form-js check-type-2-js" style="display: none;">
							<label class="qr-label-js">
								<span>Фото QR-кода крупным планом<i>*</i></span>
								<input name="qr" type="file">
							</label>
							<div class="hidden-fields qr-props-js" style="display: none;">
								<label>
									<span>Дата указанная в чеке<i>*</i></span>
									<input name="date" type="date" placeholder="" required readonly>
								</label>
								<label>
									<span>Время указанное в чеке<i>*</i></span>
									<input name="time" type="time" required readonly />
								</label>
								<label>
									<span>Цена указанная в чеке<i>*</i></span>
									<input name="price" type="text" placeholder="Цена" readonly required>
								</label>
								<label>
									<span>Тип расхода<i>*</i></span>
									<select name="type" required>
										<option value="">Выберете тип чека</option>
										<option value="Проживание">Проживание</option>
										<option value="Питание">Питание</option>
										<option value="Проезд">Проезд</option>
									</select>
								</label>
								<label>
									<span>Тип расхода (пометка)</span>
									<input name="type_comment" type="text" placeholder="Тип расхода (пометка)">
								</label>
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