<?php

use AlegzaCRM\AlegzaAPI\AlegzaAPI;
use AlegzaCRM\AlegzaAPI\Exceptions\APIException;
use AlegzaCRM\AlegzaAPI\Models\Contract;
use AlegzaCRM\AlegzaAPI\Models\Person;
use AlegzaCRM\AlegzaAPI\Models\Post;

require __DIR__ . '../../vendor/autoload.php';

$alegza = new AlegzaAPI(
    'https://test.alegza.hu',
    'apitest@alegza.hu',
    'api12345678'
);

try {

    // Rögzítünk egy új személyt
    $newPerson = $alegza->newPerson(new Person([
        'full_name' => 'API Személy',
        'age' => 24,
        'city' => 'Kecel',
        'phone' => '+36803344556',
        'relationship_state' => 1
    ]));

    // Átnevezzük
    $newPerson->full_name = "API Személy Update";
    $alegza->updatePerson($newPerson);

    // Rögzítünk hozzá egy új bejegyzést
    $newPost = $alegza->newPost(new Post([
        'person' => $newPerson->id,
        'type' => 3,
        'post_timestamp' => Date('Y-m-d H:i:s'),
        'message' => 'Visszahívást kért',
        'success' => null
    ]));

    // Módosítjuk a bejegyzést
    $newPost->message = "Visszahívást kért ma délutánra.";
    $alegza->updatePost($newPost);

    // Lekérjük az új személy összes bejegyzését
    print_r(
        $alegza->getPersonsPosts($newPerson->id)
    );

    // Töröljük a bejegyzést
    $alegza->deletePost($newPost);

    // Lekérjük a termékeket, kötünk egy új szerződést az elsőre, ami az adatbázisban van
    $products = $alegza->getProducts();
    $newContract = $alegza->newContract(new Contract([
        'product' => $products[0]->id,
        'time' => Date('Y-m-d H:i:s'),
        'technical_start' => Date('Y-m-d H:i:s'),
        'bond_number' => 'APITEST-1234',
        'person' => $newPerson->id,
        'notes' => 'alegza-crm-api csomaggal készült szerződés',
        'post' => null
    ]));

    // Lekérjük az új személy szerződéseit
    print_r(
        $alegza->getPersonsContracts($newPerson->id)
    );

    // Töröljük a személyt
    $alegza->deletePerson($newPerson);

} catch (APIException $exception)
{
    /*
     * APIException típusú Exception keletkezik, ha az API küld hibát. A részletek (pl. validációs hiba esetén a
     * hibás mezők) az errors tömbben vannak felsorolva.
    */
    print_r([
        'Message' => $exception->getMessage(),
        'Errors' => $exception->getErrors()
    ]);
}