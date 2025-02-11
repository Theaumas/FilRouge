<?php 

    namespace App\Classes; 

    use Mailjet\Client;
    use Mailjet\Resources;



    class Mail 
    {

        // Fonction permettant l'envoi d'un mail a l'arrivé sur la page d'acceuil ( pas nécessaire mais mis en place pour des tests.)
        //public function send($to_email, $to_name, $subject, $content)
        // public function send($to_email, $to_name, $subject, $template, $vars)
        // {
        //     //Récupérer le chemin en cours 
        //     //dd(dirname(__DIR__).'mail/welcome.html');
        //     //Récupérer le contenu du fichier html
        //     $content = file_get_contents(dirname(__DIR__).'/mail/'.$template);
        //     if ($vars) {
        //         foreach ($vars as $key => $var){
        //             $content = str_replace('{' . $key . '}', $var, $content);
        //         }
        //     }

        //     //MailJet
        //     $mj = new Client($_ENV["MJ_APIKEY_PUBLIC"],$_ENV["MJ_APIKEY_PRIVATE"], true, ['version' => 'v3.1']);

        //     // Define your request body

        //     $body = [
        //         'Messages' => [
        //             [
        //                 'From' => [
        //                     'Email' => "thomascancre@gmail.com", // adresse email qui à été validé par mailjet
        //                     'Name' => "Projects solution"
        //                 ],
        //                 'To' => [
        //                     [
        //                         'Email' => $to_email,
        //                         // "managing.projects@yopmail.com",
        //                         'Name' => $to_name
        //                         // "You"
        //                     ]
        //                 ],
        //                 // L'ID Template correspond aux templates créer sur MailJet
        //                 // 'TemplateID' => 6714168,
        //                 // 'TemplateLanguage' => true,
        //                 'Variables' => [
        //                     "content" => $content
        //                 ],
        //                 'Subject' => $subject,
        //                 // "My first Mailjet Email!",
        //                 // 'TextPart' => "Greetings from Mailjet!",
        //                 //HTMLPART Contient le contenu du mail qui est récupérer depuis $content plus haut qui elle même récupére le contenu du fichier welcome.html dans le dossier mail. 
        //                 'HTMLPart' => $content
        //                 // "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3>
        //                 // <br />May the delivery force be with you!"
        //             ]
        //         ]
        //     ];
        //     // All resources are located in the Resources class

        //     // $response = 
        //     $mj->post(Resources::$Email, ['body' => $body]);

        //     // Read the response

        //     // $response->success() && var_dump($response->getData());
        // }
    }