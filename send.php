<?php
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
//função que envia publicação automaticamente
 function share_post($link,$title,$description,$img_url){
    //processo de edição do texto antes do envio
    $description.="\n
    Gostou da noticia? curta, comente e compartilhe para mais pessoas ficarem por dentro.
    \n\n Essa publicação é gerada automaticamente pelo AutoPub,\n #ti #noticias #tecnologia #news
    :) 
    ";
    $link = $link;
    $access_token = ''; //token de acesso do Linkedin
    $linkedin_id = ''; //Linkedin ID PROFILE

    $body = new \stdClass();
    $body->content = new \stdClass();
    $body->content->contentEntities[0] = new \stdClass();
    $body->text = new \stdClass();
    $body->content->contentEntities[0]->thumbnails[0] = new \stdClass();
    $body->content->contentEntities[0]->entityLocation = $link;
    $body->content->contentEntities[0]->thumbnails[0]->resolvedUrl = $img_url; //link da url da imagem
    $body->content->title = $title; //titulo
    $body->owner = 'urn:li:person:'.$linkedin_id;
    $body->text->text = $description; //texto
    
    
    //visivelmente para o publico
    $body->distribution= new \stdClass();
    $body->distribution->linkedInDistributionTarget = new \stdClass();
    $body->distribution->linkedInDistributionTarget->visibleToGuest=true;

    $body_json = json_encode($body, true);
      
    try {
        $client = new Client(['base_uri' => 'https://api.linkedin.com']);
        $response = $client->request('POST', '/v2/shares', [
            'headers' => [
                "Authorization" => "Bearer " . $access_token,
                "Content-Type"  => "application/json",
                "x-li-format"   => "json"
            ],
            'body' => $body_json,
        ]);
      
        if ($response->getStatusCode() !== 201) {
            echo 'Error: '. $response->getLastBody()->errors[0]->message;
        }
      
        //echo 'Post is shared on LinkedIn successfully.';
        return true;
    } catch(Exception $e) {
        echo $e->getMessage(). ' for link '. $link;
        return false;
    }



 } 