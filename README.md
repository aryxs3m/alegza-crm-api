# alegza-crm-api

Ez a csomag segít az Alegza CRM API-jának használatához PHP alól. Legalább PHP 7.0 szükséges hozzá.

Licensz: [MIT](LICENSE.md)

Alegza CRM weboldala: [https://alegza.hu](https://alegza.hu)

Kapcsolatfelvétel a csomag fejlesztőjével: [aryxs3m (Tóth Patrik)](mailto:toth.patrik@alegza.hu)

---

## Telepítés
A csomag telepíthető composerrel a
```
composer require aryxs3m/alegza-crm-api
```
parancs kiadásával.

## Példa

Az [examples/](examples/) mappában elérhető egy példa, ami a legtöbb funkció működését bemutatja.

## Használat

### Példakód
Példa egy személy létrehozására:

```php
$alegza = new AlegzaAPI(
    'https://test.alegza.hu',
    'apitest@alegza.hu',
    'api12345678'
);

try {
    $newPerson = $alegza->newPerson(new Person([
        'full_name' => 'Teszt Személy',
        'age' => 24,
        'city' => 'Kecel',
        'phone' => '+36803344556',
        'relationship_state' => 1
    ]));
}
catch (APIException $exception)
{
    echo "API hiba: {$exception->getMessage()}";
}

```

### Modellek

Az csomag az API válaszait modellekké alakítja, illetve ilyen modelleket létrehozva lehet adatot beküldeni és meglévő
erőforrásokat módosítani is. A modellek attribútumai megegyeznek az Alegza API dokumentációban található 
attribútumokkal.

Például egy bejegyzés lekéréséből `Post` típusú osztály jön létre:
```
AlegzaCRM\AlegzaAPI\Models\Post Object
(
    [id] => 53
    [created_at] => 2021-07-18T13:54:19.000000Z
    [updated_at] => 2021-07-18T13:54:19.000000Z
    [person] => 10606
    [type] => 3
    [post_timestamp] => 2021-07-18T11:54:19.000000Z
    [message] => Visszahívást kért ma délutánra.
    [success] => 
    [deleted_at] => 
    [user_id] => 
)
```