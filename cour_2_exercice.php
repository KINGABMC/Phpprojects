<?php
// Enumération pour les choix du menu
enum Menu: int {
    case AjouterClient = 1;
    case ListerClients = 2;
    case ListerDettesClient = 3;
    case PayerDette = 4;
    case Quitter = 5;
}

// Fonctions Access aux données
function selectClients(): array {
    return [
        [
            "telephone" => "123456789",
            "nom" => "Doe",
            "prenom" => "John",
            "adresse" => "123 Main St",
            "dettes" => []
        ],
        [
            "telephone" => "987654321",
            "nom" => "Smith",
            "prenom" => "Jane",
            "adresse" => "456 Elm St",
            "dettes" => []
        ]
    ];
}

function selectClientByTel(array $clients, string $tel): array|null {
    foreach ($clients as $client) {
        if ($client["telephone"] === $tel) {
            return $client;
        }
    }
    return null;
}

function insertClient(array &$tabClients, array $client): void {
    $tabClients[] = $client;
}

// Gestion des dettes
function ajouterDette(array &$clients, string $tel): void {
    $date = date("Y-m-d H:i:s");
    $reference = uniqid();
    foreach ($clients as &$client) {
        if ($client["telephone"] === $tel) {
            $client["dettes"][] = [
                "reference" => $reference,
                "montant" => (int)readline("Entrez le montant de la dette: "),
                "date" => $date,
                "montant_versé" => (int)readline("Entrez le montant versé de la dette: ")
            ];
            break;
        }
    }
}

function listerDettes(array $client): void {
    if (empty($client["dettes"])) {
        echo "Aucune dette enregistrée pour ce client.\n";
    } else {
        foreach ($client["dettes"] as $dette) {
            echo "Référence: {$dette['reference']}, Montant: {$dette['montant']} FCFA, Date: {$dette['date']}, Montant Versé: {$dette['montant_versé']} FCFA\n";
        }
    }
}

function payerDette(array &$clients, string $tel): bool {
    foreach ($clients as &$client) {
        if ($client["telephone"] === $tel) {
            $montant_verse = (int)readline("Entrez le montant versé de la dette: ");
           foreach ($client["dettes"] as &$dette) {
                    $reste = $dette["montant"] - $montant_verse;
                    if ($montant_verse > $reste) {
                        echo "Montant supérieur au reste dû.\n";
                        return false;
                    }
                    $dette["montant_versé"] += $montant_verse;
                    echo "Dette mise à jour avec succès.\n";
                    return true;
            }
        }
    }
    echo "Dette introuvable.\n";
    return false;
}

// Fonctions Services
function enregistrerClient(array &$tabClients, array $client):bool {
    $tel = $client["telephone"];
    $result = selectClientByTel($tabClients, $client["telephone"]);
    if ($result === null) {
        insertClient($tabClients, $client);
        ajouterDette($tabClients, $tel);
        return true;
    }
    return false;
}

function listerClient(): array {
    return selectClients();
}

// Fonctions Présentation
function saisieChampObligatoire(string $sms): string {
    do {
        $value = readline($sms);
    } while (empty($value));
    return $value;
}


function saisieTelephone_Obligatoireandunique(array $clients, string $sms): string {
    do {
        $value = trim(readline($sms));
        if (!preg_match("/^[0-9]{9}$/", $value)) {
            echo "Le numéro de téléphone doit avoir 9 chiffres.\n";
            continue;
        }else if (selectClientByTel($clients, $value) !== null) {
            echo "Le numéro de numéro existe déjà.\n";
            continue;
        }else {
            return $value;
        }
    } while (true);
    
}
function saisie_telephone(string $sms): string {
    do {
        $value = trim(readline($sms));
        if (!preg_match("/^[0-9]{9}$/", $value)) {
            echo "Le numéro de téléphone doit avoir 9 chiffres.\n";
           }else {
            return $value;
              }
    } while (true);
}
function afficheClient(array $clients): void {
    if (count($clients) == 0) {
        echo "Pas de client à afficher.\n";
    } else {
        foreach ($clients as $client) {
            echo "\n-----------------------------------------\n";
            echo "Téléphone : " . $client["telephone"] . "\t";
            echo "Nom : " . $client["nom"] . "\t";
            echo "Prénom : " . $client["prenom"] . "\t";
            echo "Adresse : " . $client["adresse"] . "\n";
        }
    }
}

function saisieClient(array $clients): array {
    return [
        "telephone" => saisieTelephone_Obligatoireandunique($clients, "Entrez le Téléphone: "),
        "nom" => saisieChampObligatoire("Entrez le Nom: "),
        "prenom" => saisieChampObligatoire("Entrez le Prénom: "),
        "adresse" => saisieChampObligatoire("Entrez l'Adresse: "),
        "dettes" => []
    ];
}

function menu(): Menu {
    echo "
     1. Ajouter client
     2. Lister les clients
     3. Lister les dettes d'un client
     4. Payer une dette
     5. Quitter\n";
    $choix = (int)readline("Faites votre choix: ");
    return Menu::from($choix);
}

// Fonction principale
function principal() {
    $clients = selectClients();
    do {
        $choix = menu();
        match ($choix) {
            Menu::AjouterClient => enregistrerClient($clients, saisieClient($clients)) ? print("Client enregistré avec succès.\n") : print("Le numéro de téléphone existe déjà.\n"),
            Menu::ListerClients => afficheClient($clients),
            Menu::ListerDettesClient => listerDettes(
                selectClientByTel($clients, saisie_telephone("Entrez le téléphone du client: ")) ?? []
            ),
            Menu::PayerDette => payerDette(
                $clients,
                saisie_telephone("Entrez le téléphone du client: "),
            ),
            Menu::Quitter => print("Au revoir à la prochaine!\n"),
            default => print("Choix invalide. Réessayez.\n"),
        };
    } while ($choix !== Menu::Quitter);
}

// Exécution du programme
principal();
