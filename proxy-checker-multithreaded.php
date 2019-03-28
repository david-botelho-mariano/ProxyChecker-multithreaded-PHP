<?php

$mh = curl_multi_init();
$open_file = file("proxies_2019_03_28.txt");

$quantidade_thread_e_proxy = 50;

$proxy_list = [];
for ($x=0; $x <= $quantidade_thread_e_proxy; $x++) { 
	$proxy_random = $open_file[array_rand($open_file)];
	array_push($proxy_list,$proxy_random);
}
//carrega a matriz com proxies aleatorios
//print_r($proxy_list);


for ($x = 0; $x <= $quantidade_thread_e_proxy; $x++) {
	$ch[$x] = curl_init('https://www.google.com.br/?gws_rd=ssl');
	curl_setopt($ch[$x],CURLOPT_PROXY,$proxy_list[$x]);
	curl_setopt($ch[$x],CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch[$x],CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch[$x],CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($ch[$x],CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch[$x],CURLOPT_TIMEOUT,10);
	curl_setopt($ch[$x],CURLOPT_HTTPHEADER,array(
		"User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0",
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		"Accept-Language: en-US,en;q=0.5",
		"DNT: 1",
		"Upgrade-Insecure-Requests: 1",
		"Connection: close"
	));
	//configura cada curl com um proxy diferente
	curl_multi_add_handle($mh, $ch[$x]);
	//adicionar cada curl para o multi-curl
}

	$executando = null;
  	do {
    	curl_multi_exec($mh, $executando);
    	//usleep(15);
	} while ($executando);
	//mantem as requisicoes enquanto eles estiverem sendo processadas

foreach($ch as $id => $get_dados) {
	$conteudo[$id] = curl_multi_getcontent($get_dados);
	//pegar o conteudo dos curls
	curl_multi_remove_handle($mh,$get_dados);
	//limpa os curls do multi-curl
	if (strpos($conteudo[$id], 'href="https://www.google.com/calendar?tab=wc"') == true) {
		echo "[-]Proxy funcionando -> ".$proxy_list[$id]."\n";
		//echo "[~]Resposta da requisicao -> ".$conteudo[$id]."\n";
		//break;
	}
}
?>