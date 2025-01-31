<?php 

    // On peut faire deux cas différents soit par texte soit par case a cocher pour le filtrage. 

    namespace App\Classes;

    class Search{

        //  L'interface de recherche en texte
        public $string = ''; 

        // L'interface de recherche avec tout les attributs compris dans user 
        public $users = [];

        public function __toString()
        {
            // le implode effectue la séparation avec une virgule
            return $this->string . implode(',', $this->users);
        }
    
    }