<?php

namespace AlegzaCRM\AlegzaAPI;

use AlegzaCRM\AlegzaAPI\Exceptions\APIException;
use AlegzaCRM\AlegzaAPI\Models\AlegzaModel;
use AlegzaCRM\AlegzaAPI\Models\Contract;
use AlegzaCRM\AlegzaAPI\Models\Person;
use AlegzaCRM\AlegzaAPI\Models\Post;
use AlegzaCRM\AlegzaAPI\Models\PostType;
use AlegzaCRM\AlegzaAPI\Models\Product;
use AlegzaCRM\AlegzaAPI\Models\ProductProviders;
use AlegzaCRM\AlegzaAPI\Models\ProductType;
use AlegzaCRM\AlegzaAPI\Models\Response;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Class AlegzaAPI
 *
 * Alegza CRM API-jához készült PHP interfész.
 *
 * @package AlegzaCRM\AlegzaAPI
 * @author Tóth Patrik <toth.patrik@alegza.hu>
 */
class AlegzaAPI
{
    /**
     * @var Client GuzzleHttp Client
     */
    private $connection;

    /**
     * AlegzaAPI constructor.
     * @param string $url Munkaterület URL-je. Ne tartalmazza a záró perjelet!
     * @param string $username E-mail cím
     * @param string $password Jelszó
     * @param int $timeout Kérések időtúllépése másodpercben. Alapértelmezetten 30.
     */
    public function __construct(string $url, string $username, string $password, int $timeout = 30)
    {
        $this->connection = new Client([
            'base_uri' => $url,
            'auth' => [$username, $password],
            'timeout' => $timeout
        ]);
    }

    /**
     * Segédfüggvény, ami az API-tól érkező válaszokat AlegzaModel modellekké és APIException exceptionökké alakítja.
     *
     * @param ResponseInterface $response GuzzleHttp válasz
     * @param string $class AlegzaModel model osztály
     * @param bool $asArray Ha true, egy tömbnyni $class-t ad vissza és tömböt is vár az API-tól
     * @return array|mixed Tömb, vagy a kért $class model
     * @throws APIException API hiba esetén
     */
    private function fetchData(ResponseInterface $response, string $class, bool $asArray = false)
    {
        $data = json_decode($response->getBody()->getContents(), true);

        if (array_key_exists('error', $data))
        {
            throw new APIException($data['error'], $data['errors']);
        }

        if ($asArray)
        {
            $objects = [];
            foreach($data as $entry)
            {
                $object = new $class();
                $object->create($entry);
                $objects[] = $object;
            }
            return $objects;
        }
        else
        {
            $object = new $class();
            $object->create($data);
            return $object;
        }
    }


    /** ---- Személyek ---- **/

    /**
     * Visszaad egy személyt az azonosítója alapján
     * @param int $id Személy azonosítója
     * @return Person
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPerson(int $id) : Person
    {
        return $this->fetchData(
            $this->connection->get("/api/persons/{$id}"),
            Person::class
        );
    }

    /**
     * Visszaad minden személyt
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPersons() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/persons"),
            Person::class,
            true
        );
    }

    /**
     * Létrehoz egy új személyt
     * @param Person $person
     * @return Person
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newPerson(Person $person) : Person
    {
        return $this->fetchData(
            $this->connection->post("/api/persons",
            [
                'json' => $person->asArray()
            ]),
            Person::class
        );
    }

    /**
     * Módosít egy személyt. A személy azonosítóját a Person modelből veszi, vagy ha meg van adva az $id, akkor onnan
     * @param Person $person
     * @param int|null $id
     * @return Person
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePerson(Person $person, int $id = null) : Person
    {
        if (is_null($id))
        {
            $id = $person->id;
        }

        return $this->fetchData(
            $this->connection->put("/api/persons/{$id}",
            [
                'json' => $person->asArray()
            ]),
            Person::class
        );
    }

    /**
     * Töröl egy személyt az azonosítója alapján
     * @param int $id
     * @return Response
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePersonById(int $id) : Response
    {
        return $this->fetchData(
            $this->connection->delete("/api/persons/{$id}"),
            Response::class
        );
    }

    /**
     * Töröl egy személyt model alapján
     * @param Person $person
     * @return Response
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePerson(Person $person) : Response
    {
        return $this->deletePersonById($person->id);
    }


    /** ---- Bejegyzések ---- **/

    /**
     * Visszaadja az összes bejegyzéstípust
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPostTypes() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/eventtypes"),
            PostType::class,
            true
        );
    }

    /**
     * Visszaad egy bejegyzést az azonosítója alapján
     * @param int $id
     * @return Post
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPost(int $id) : Post
    {
        return $this->fetchData(
            $this->connection->get("/api/events/{$id}"),
            Post::class
        );
    }

    /**
     * Visszaadja az összes bejegyzést
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPosts() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/events"),
            Post::class,
            true
        );
    }

    /**
     * Visszaadja egy személy összes azonosítóját
     * @param int $person_id Személy azonosítója
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPersonsPosts(int $person_id) : array
    {
        return $this->fetchData(
            $this->connection->get("/api/persons/{$person_id}/events"),
            Post::class,
            true
        );
    }

    /**
     * Létrehoz egy új bejegyzést
     * @param Post $post
     * @return Post
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newPost(Post $post) : Post
    {
        return $this->fetchData(
            $this->connection->post("/api/persons/events",
                [
                    'json' => $post->asArray()
                ]),
            Post::class
        );
    }

    /**
     * Módosít egy bejegyzést. Az azonosítót a Post modelből veszi, vagy ha van $id megadva, akkor onnan
     * @param Post $post
     * @param int|null $id
     * @return Post
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePost(Post $post, int $id = null) : Post
    {
        if (is_null($id))
        {
            $id = $post->id;
        }

        return $this->fetchData(
            $this->connection->put("/api/persons/events/{$id}",
                [
                    'json' => $post->asArray()
                ]),
            Post::class
        );
    }

    /**
     * Töröl egy bejegyzést az azonosítója alapján
     * @param int $id
     * @return Response
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePostById(int $id) : Response
    {
        return $this->fetchData(
            $this->connection->delete("/api/persons/events/{$id}"),
            Response::class
        );
    }

    /**
     * Töröl egy bejegyzést model alapján
     * @param Post $post
     * @return Response
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePost(Post $post) : Response
    {
        return $this->deletePostById($post->id);
    }


    /** ---- Szerződések ---- **/

    /**
     * Visszaad egy szerződést azonosító alapján
     * @param int $id
     * @return Contract
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContract(int $id) : Contract
    {
        return $this->fetchData(
            $this->connection->get("/api/persons/contracts/{$id}"),
            Contract::class
        );
    }

    /**
     * Visszaadja az összes szerződést
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContracts() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/contracts"),
            Contract::class,
            true
        );
    }

    /**
     * Visszaadja egy személy összes szerződését
     * @param int $person_id Személy azonosítója
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPersonsContracts(int $person_id) : array
    {
        return $this->fetchData(
            $this->connection->get("/api/persons/{$person_id}/contracts"),
            Contract::class,
            true
        );
    }

    /**
     * Létrehoz egy új szerződést
     * @param Contract $contract
     * @return Contract
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newContract(Contract $contract) : Contract
    {
        return $this->fetchData(
            $this->connection->post("/api/persons/contracts",
                [
                    'json' => $contract->asArray()
                ]),
            Contract::class
        );
    }

    /**
     * Módosít egy szerződést. A Contract modelből veszi az azonosítót, vagy ha van megadva $id, akkor onnan
     * @param Contract $contract
     * @param int|null $id
     * @return Contract
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateContract(Contract $contract, int $id = null) : Contract
    {
        if (is_null($id))
        {
            $id = $contract->id;
        }

        return $this->fetchData(
            $this->connection->put("/api/persons/contracts/{$id}",
                [
                    'json' => $contract->asArray()
                ]),
            Contract::class
        );
    }


    /** ---- Termékek, termékkategóriák ---- **/

    /**
     * Visszaadja az összes terméket
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProducts() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/products"),
            Product::class,
            true
        );
    }

    /**
     * Visszaadja az összes terméktípust (termékkategóriát)
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProductTypes() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/producttypes"),
            ProductType::class,
            true
        );
    }

    /**
     * Visszaadja az összes szolgáltatót
     * @return array
     * @throws APIException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProductProviders() : array
    {
        return $this->fetchData(
            $this->connection->get("/api/productproviders"),
            ProductProviders::class,
            true
        );
    }
}