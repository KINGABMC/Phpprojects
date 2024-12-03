<?php 
//Fonctions Access aux donnees
   function selectClients():array {
    return
        [
            [
            "nom"=>"Wane",
            "prenom"=>"Baila",
            "telephone"=>"777661010",
            "adresse"=>"FO"

            ],
            [
            "nom"=>"Wane1",
            "prenom"=>"Baila1",
            "telephone"=>"777661011",
            "adresse"=>"FO1"

            ]
        ];


   }

   function selectClientByTel(array $clients,string $tel):array|null {
        foreach ($clients as  $client) {
            if ($client["telephone"] == $tel) {
               return $client;
            }
        }
        return null;
   }

   function insertClient(array &$tabClients,$client):void {
          // array_push($tabClients,$client);
           $tabClients[]=$client;
      }




//Fonctions Services ou Use Case  ou Metier
  function  enregistrerClient(array &$tabClients,array $client):bool{
     $result=  selectClientByTel($tabClients,$client["telephone"]);
     if (  $result==null ) {
        insertClient($tabClients,$client);
        return true;
     }
     return false;
  }

  function listerClient():array{
      return selectClients();
  }


function estVide(string $value):bool{
    //$value=="" ou empty($value)
    return empty($value);
}




//Fonctions Presentation
function saisieChampObligatoire(string $sms):string{
    do {
        $value= readline($sms);
    } while (estVide($value));
   return $value;
}
function telephoneIsUnique(array $clients,string $sms):string{
    do {
        $value= readline($sms);
    } while (estVide($value) || selectClientByTel($clients,$value)!=null);
    return $value;
   
}

function afficheClient(array $clients):void{
    if (count($clients)==0) {
        echo "Pas de client a affiche";
    }else {
        foreach ($clients as  $client) {
            echo"\n-----------------------------------------\n";
            echo "Telephone : ". $client["telephone"]."\t";
            echo "Nom : ". $client["nom"]."\t";
            echo "Prenom : ". $client["prenom"]."\t";
            echo "Adresse : ". $client["adresse"]."\t";
      }
    }
    
}



function saisieClient(array $clients):array{
    return [
        "telephone"=>telephoneIsUnique($clients,"Entrer le Telephone: "),
         "nom"=>saisieChampObligatoire(" Entrer le Nom: "),
         "prenom"=>saisieChampObligatoire(" Entrer le Prenom: "),
         "adresse"=>saisieChampObligatoire(" Entrer l'Adresse: "),
    ] ; 
}
function menu():int{
    echo "
     1.Ajouter client \n
     2.Lister les clients\n 
     3.Rechercher client par telephone\n
     4.Quitter\n";
    return (int)readline(" Faites votre choix: ");
}




function principal(){
   $clients= selectClients();
   do {

      $choix= menu();
      switch ($choix) {
       case 1:
        $client=saisieClient($clients);
       if (enregistrerClient($clients,  $client)) {
           echo"Client Enregistrer avec success \n";
       }else {
            echo"Le numero Telephone  existe deja \n";
       } 
       break;
       case 2:
        afficheClient( $clients);
       break;
       case 3:
           # code...
       break;
       case 4:
           # code...
       default:
          echo "Veullez faire un bon choix: ";
           break;
      }

   } while ($choix!=4);
}
principal();


