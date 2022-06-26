<?php
//variaveis de configuração do nosso script
$senha=md5("autopub-cyber");
$api_key=""; // Token  da Api do site NewsData.io
require_once 'send.php';
//token do linkedin e configs
$id_client="";
$token="";

//verificação do token de acesso
if (!empty($_GET['token']) ){
    $token=addslashes($_GET['token']);
    if ($token==$senha){
    $noticia=get_noticias($api_key);
    $status=publish_linkedin($noticia);
    if ($status==true){
        echo "Noticia Publicada som sucesso";
    }else{
        echo "Autenticado, porem o envio falhou";
    }
    }else{
        echo "Permissão negada.";
    }
}else{
    echo "Sem token....";
}

function get_noticias($api_key){
    // GET Noticias From NewsApi
    
    //$config="https://newsapi.org/v2/top-headlines?country=br&pageSize=1&category=technology&apiKey=".$api_key;
    $url = 'https://newsdata.io/api/1/news?country=br&language=pt&category=technology&apikey=';
    $apiKey = $api_key;
    $curl = curl_init($url.$apiKey);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
    $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
    curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
    $json_noticias = curl_exec($curl);
    return $json_noticias;
    curl_close($curl);
}

function publish_linkedin($noticia){
   $a=0;
   $title;
   $link;
   $keywords=array();
   $keyword_string;
   $descricao;
   $conteudo;
   $img_url;
   //pegando dados do JSOn para elaboração da Noticia
   $obj=json_decode($noticia);
   foreach ($obj->results as $new){
       if ($a==0){
           
         //processo de setar as variaveis criadas....
         $title=$new->title;
         $link=$new->link;
         $keywords=$new->keywords;
         $descricao=$new->description;
         $conteudo=$new->content;
         $img_url=$new->image_url;
         
         //processo de tratamento das informações
         for($i=0;$i<count($keywords);$i++){
             $keyword_string.="#".$keywords[$i]." ";
         }
        
        $a++;
       }else{
           break;
       }
       
   }
   
   $conteudo=$descricao."\n\n".$conteudo."\n\n".$keyword_string;
  
   if (empty($img_url)){
       $img_url="https://previews.123rf.com/images/maxkabakov/maxkabakov1305/maxkabakov130500140/19619832-news-concept-pixelated-words-tech-news-on-digital-background-3d-render.jpg";
   }
   //processo de publicação no linkedin
   $s=share_post($link,$title,$conteudo,$img_url);
   return $s;
  
}

?>