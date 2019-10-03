<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class CrawlerController extends Controller
{
    public function crawler(){
        $client = new Client();
        $domDoc = new \DOMDocument();
        $url = 'http://www.guiatrabalhista.com.br/guia/salario_minimo.htm';

        $res = $client->request('GET', $url);


        $html = (string)$res->getBody();

        // The @ in front of $domDoc will suppress any warnings
        @$domDoc->loadHTML($html);


        // get the third table
        $thirdTable = $domDoc->getElementsByTagName('table')->item(0);

        $array = [];
        // iterate over each row in the table
        foreach($thirdTable->getElementsByTagName('tr') as $tr)
        {            
            $tds = $tr->getElementsByTagName('td'); // get the columns in this row

            // example
            // [0] => Array {
            //     ['vigencia'] = 01.01.2018
            //     ['valor_mensal'] = R$954,00
            if($tds->length >= 6 && trim($tds->item(0)->nodeValue) != 'VIGÊNCIA')
            {

                    $mount = [
                        'vigencia' =>  trim($tds->item(0)->nodeValue),
                        'valor_mensal' =>  trim($tds->item(1)->nodeValue),
                        'valor_diario' =>  trim($tds->item(2)->nodeValue),
                        'valor_hora' =>  trim($tds->item(3)->nodeValue),
                        'norma_legal' =>  trim($tds->item(4)->nodeValue),
                        'dou' =>  trim($tds->item(5)->nodeValue),

                    ];
                    array_push($array, $mount);
            }
        }
        dd($array); // debug
    }
}
