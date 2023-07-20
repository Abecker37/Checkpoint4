<?php
namespace App\Service;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GiantBombAPI
{
    private $apiKey;
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $apiKey = "e10eff29e71fe87534904d3f8bda615e50082f21")
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

   public function searchGame(string $gameName)
    {
        $encodedGameName = urlencode($gameName);

        $apiUrl = "https://www.giantbomb.com/api/search/?api_key={$this->apiKey}&format=json&query={$encodedGameName}&limit=10&resources=game";

        
        try {
            $response = $this->httpClient->request('GET', $apiUrl);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $game = null;

                if (isset($data['results'])) {
                    $game = $data['results'];
                }

                if ($game !== null) {
                    return $game;
                }
            }

            throw new NotFoundHttpException("Le jeu '{$gameName}' n'a pas été trouvé dans l'API.");
        } catch (ExceptionInterface $e) {
            throw new \Exception("Erreur lors de la requête à l'API Giant Bomb : " . $e->getMessage());
        }
    }

    public function findGame(string $guid): array
    {
        $encodedGuid = urlencode($guid);
        $apiUrl = "https://www.giantbomb.com/api/game/{$encodedGuid}?api_key={$this->apiKey}&format=json";
         try {
            $response = $this->httpClient->request('GET', $apiUrl);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $game = null;

                if (isset($data['results'])) {
                    $game = $data['results'];
                }

                if ($game !== null) {
                    return $game;
                }
            }

            throw new NotFoundHttpException("Le GUUID '{$guid}' n'a pas été trouvé dans l'API.");
        } catch (ExceptionInterface $e) {
            throw new \Exception("Erreur lors de la requête à l'API Giant Bomb : " . $e->getMessage());
        }
    }
}
