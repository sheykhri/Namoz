<?php

ini_set("memory_limit","-1");
require './class-http-request.php';

function bot($method, $datas = []){
	$url = "https://api.telegram.org/bot945838095:AAFCI9C7y7h7ln5QBsutTAUFeMl5qbB8xHs/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
		mkdir('crash');
		file_put_contents('crash/bot_function_' . time() . '.log', var_dump(ecurl_error($ch)));
    } else {
        return json_decode($res);
    }
}

$update = json_decode(file_get_contents('php://input'));

if (isset($update)) {
	
	if (isset($update->message)) {
		$message = $update->message;
		$chat_id = $message->chat->id;
		$text = $message->text;
	}
	
}

function js($json) {
	if ($json) {
	
	}
}

if ($text) {

	$informations = new HttpRequest("GET", "https://namaz.today/city/riga");
	$html = HTML($informations->getResponse());
	
	foreach ($html->find('span.text-center') as $key => $vaqt) {
		$vaqtlar[$key] = trim(strip_tags(pq($vaqt)));
	}
	
	$now = str_replace('#day_time_', '', $html->find('div.active a')->attr('href'));
	$soat = trim(strip_tags($html->find('div.active .time-remaining-content span.hour_reverse')->text()));
	$minut = trim(strip_tags($html->find('div.active .time-remaining-content span.minute_reverse')->text()));
	
	if ($text == '/ru') {
		$nomlar = ["Фаджр", "Восход", "Зухр", "Аср", "Магриб", "Иша"];
		$matn = '<b>☪Время Намазов</b>';
		$matn .= "\n";
		
		$pasti = "\n\n⏳Сейчас <b>";
		$pasti .= $nomlar[$now] . "</b>\n";
		$pasti .= '📿Следующий <b>Намаз ';
		
		$ru = str_replace(['До ', '  ', 'осталось'], ['', '', 'осталось '], trim(strip_tags($html->find('div.active .time-remaining-content')->text())));
		$ru = str_replace(["Фаджра", "Восхода", "Зухра", "Асра", "Магриба", "Иша"], ['Фаджр</b>', 'Восход</b>', 'Зухр</b>', 'Аср</b>', 'Магриб</b>', 'Иша</b>'], $ru);
		if ($soat == '00') $ru = str_replace(' 00 часов', '', $ru);
		if ($minut == '00') $ru = str_replace('00 минуты', '', $ru);
		$pasti .= $ru;
	}
	
	if ($text == '/uz') {
		$nomlar = ["Bomdod", "Quyosh", "Peshin", "Asr", "Shom", "Xufton"];
		$matn = '<b>☪Namoz vaqtlari</b>';
		$matn .= "\n";
		
		$pasti = "\n\n⏳Hozir <b>";
		$pasti .= $nomlar[$now] . "</b>\n";
		$pasti .= '📿Keyingi <b>Namoz "';
		
		$uz = trim(str_replace(['До ', ' осталось', 'час', 'минут', 'а', '  ', 'ов', 'ы'], ['', '"</b> gacha ', 'soat va', 'daqiqa qoldi.', '', '', '', ''], str_replace(["Фаджр", "Восход", "Зухр", "Аср", "Магриб", "Иша"], $nomlar, trim(strip_tags($html->find('div.active .time-remaining-content')->text())))));
		if ($soat == '00') $uz = str_replace(' 00 soat va', '', $uz);
		if ($minut == '00') $uz = str_replace(' va 00 daqiqa', '', $uz);
		$pasti .= $uz;
	}
	
	foreach ($html->find('div.time-remaining-content') as $key => $info) {
		$info = pq($info);	
		$nom = $nomlar[$key];
		$vaqt = $vaqtlar[$key];
		$matn .= "\n";
		$matn .= "<b>$nom</b> $vaqt";
	}

	$matn .= $pasti;
	
	bot('sendMessage', [
		'chat_id' => $chat_id,
		'text' => $matn,
		'parse_mode' => 'HTML',
	]);
}
?>