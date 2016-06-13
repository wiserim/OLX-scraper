<?php
/**
* Copyright (c) 2016, Marcin Walczak
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
* 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
 
include_once 'simple_html_dom.php';
class OLXscraper
{
    public static $ajaxPhone = "http://olx.pl/ajax/misc/contact/phone/";
    public static function getOLXSiteData($url){
        $id = substr($url, strpos($url, '-ID')+3, (strpos($url, '.htm')-strpos($url, '-ID')-3));
        $html = file_get_html($url);

        $result=[];
        $result['title'] = trim($html->find('h1.brkword')[0]->plaintext);
        $result['user'] = trim($html->find('span.color-5')[0]->plaintext);
        $result['location'] = trim($html->find('strong.c2b')[0]->plaintext);
        try{
            $content = file_get_contents(OLXscraper::$ajaxPhone.$id);
            $result['phone'] = preg_replace('/[^0-9,]|,[0-9]*$/','', json_decode($content, true)['value']);
        }
        catch(Exception $e){
            $result['phone'] = null;
        }
		
        return $result;
    }
	
    public static function getOLXLinkList($url, $pageFrom, $pageTo){
        $result = [];
		$list = [];
        for($i=$pageFrom; $i<=$pageTo; $i++){
            $html = file_get_html($url."?page=".$i);
            $list = array_merge($list, $html->find('h3 a.link'));
        }
		
        foreach($list as $link){
			if(!(in_array($link->href, $result))) $result[] = $link->href;
        }
        return $result;
    }
	
    public static function getOLXListData($url, $pageFrom, $pageTo, $unique){
        $result = [];
        $links = OLXscraper::getOLXLinkList($url, $pageFrom, $pageTo);
        foreach($links as $link){
			$record = OLXscraper::getOLXSiteData($link);
			if($unique){
				$phones = array_column($result, 'phone');
				if(!(in_array($record['phone'], $phones))) $result[] = $record;
			}
			else $result[] = $record;
        }
        return $result;
    }

}