<?php
$curl = curl_init('http://www.reuters.com/finance/stocks');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
$html = curl_exec($curl);
curl_close($curl);

function crawler($html, $path, $p1, $p2) {
    $v1 = array();
    $v2 = array();
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $tableRows = $xpath->query($path);
    foreach ($tableRows as $row) {
        $path1 = $xpath->query($p1, $row);
        $path2 = $xpath->query($p2, $row);
        foreach($path1 as $node) {
                //echo "{$node->nodeName} - {$node->nodeValue}";
                $v1[] = $node->nodeValue;
        }
        foreach($path2 as $node) {
                //echo "{$node->nodeName} - {$node->nodeValue}";
                $v2[] = $node->nodeValue;
        }
    }
        return [$v1, $v2];
};


$results = crawler($html, "//tr[@class='dataSmall stripe']/td/a", "text()", "@href");
print_r($results);

?>