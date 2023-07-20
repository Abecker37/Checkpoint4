<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\GiantBombAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{

     #[Route('/search/results', name: 'search_results', methods: ['GET'])]
    public function searchResults(GameRepository $gameRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('searchTerm', '');
        // Récupérez les résultats de recherche depuis la base de données
        $games = $gameRepository->findAll();

        // Affichez les résultats de recherche en utilisant un template Twig
        return $this->render('game/index.html.twig', ['games' => $games, 'searchTerm' => $searchTerm]);
    }

    #[Route('/search-game', name: 'search_game', methods: ['GET'])]
    public function searchGame(Request $request, GiantBombApi $giantBombApiService, EntityManagerInterface $entityManager): Response
    {
        // Récupérez le terme de recherche à partir de la requête GET
        $gameName = $request->query->get('game_name');

        // Utilisez le service GiantBombApiService pour rechercher le jeu
        try {
            $games = $giantBombApiService->searchGame($gameName, 1);

            // Hydratez la base de données avec le premier jeu récupéré depuis l'API (limité à 1 résultat)
            if (!empty($games)) {
                $gameInfo = $games[0];

                $game = new Game();
                $game->setName($gameInfo['name']);
                $game->setDescription($gameInfo['deck']);
                $game->setPicture($gameInfo['image']['medium_url']);
                $game->setDate($gameInfo['date_added']);
                $entityManager->persist($game);
                $entityManager->flush();
            }

            // Redirigez vers la page de résultats de recherche avec le terme de recherche en tant que paramètre GET
            return $this->redirectToRoute('search_results', ['searchTerm' => $gameName]);

        } catch (\Exception $e) {
            // Gérez les erreurs, par exemple affichez un message d'erreur
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('game/{id}', name: 'game_show')]
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', ['game' => $game]);
    }


}
