<?php

$start_url = 'http://www.expatads.com/29-Brunei/posts/8-Real-Estate/223-Apartment-for-Sale/';
//This's category is name for aseanin, not crawler site.
$category = 'Real Estate';
$subcategory = 'Apartments - For Sale';


function process_html($url){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
    $html = curl_exec($curl);
    curl_close($curl);
    
    return $html;
}

function process_image($url){
    $curl = curl_init($url);
    $image_name = md5($url);
    $image_addtion_name = '.jpg';
    if(substr($url,-3,3) == 'gif')
        $image_addtion_name = '.gif';
    if(substr($url,-3,3) == 'png')
        $image_addtion_name = '.png';
    if(substr($url,-3,3) == 'bmp')
        $image_addtion_name = '.bmp';
        
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
    $rawdata = curl_exec($curl);
    curl_close($curl);
    $fp = fopen("./$image_name$image_addtion_name",'w');
    fwrite($fp, $rawdata); 
    fclose($fp);
    #return $rawdata;
}
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
                echo 'http://www.expatads.com/'.$node->nodeValue;
                
        }
    }
        return [$v1, $v2];
};

function collects_urls($html, $path, $p1, $http) {
    $v1 = array();
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $tableRows = $xpath->query($path);
    foreach ($tableRows as $row) {
        $path1 = $xpath->query($p1, $row);
        foreach($path1 as $node) {
                //echo "{$node->nodeName} - {$node->nodeValue}";
                if($http!='')
                    $v1[] = $http.$node->nodeValue;
                else
                    $v1[] = $node->nodeValue;
        }
    }
        return $v1;
};

function collects_data($html, $path, $p1) {
    $v1 = array();
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $tableRows = $xpath->query($path);
    foreach ($tableRows as $row) {
        $path1 = $xpath->query($p1, $row);
        foreach($path1 as $node) {
                $v1[] = $node->nodeValue;
        }
    }
        return $v1;
};

function collects_image($html, $path, $p1, $http) {
    $v1 = array();
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $tableRows = $xpath->query($path);
    foreach ($tableRows as $row) {
        $path1 = $xpath->query($p1, $row);
        foreach($path1 as $node) {
                if($http!=''){
                    $v1[] = $http.$node->nodeValue;
                    process_image($http.$node->nodeValue);
                }else{
                    $v1[] = $node->nodeValue;
                    process_image($node->nodeValue);
                }
        }
    }        
        return $v1;
};

$html = process_html($start_url);
$urllist = collects_urls($html, "//tr[@class='ad1']//h2/a[@class='adtitle']", "@href", 'http://www.expatads.com');
//print_r($urllist);
if($urllist){
    foreach($urllist as $node){
        $data = array();
        echo $node."\n";
        $html = process_html($node);
        $data['title'] = collects_data($html, "//span[@id='showad_title_ads']", "text()");
        $data['description'] = collects_data($html, "//div[@class='adarea']", "text()");
        $data['image'] = collects_image($html, "//a[@class='imgsmall']/img", "@rel", 'http://www.expatads.com');
        print_r($data);
    }
}
?>