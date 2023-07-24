<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\GiantBombAPI;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{

    #[Route('game/recherche', name: 'search_results', methods: ['GET'])]
    public function searchResults(Request $request, GiantBombAPI $giantBombAPI): Response
    {
        $searchTerm = $request->query->get('game_name', '');

        $games = $giantBombAPI->searchGame($searchTerm);
        return $this->render('game/index.html.twig', ['games' => $games, 'searchTerm' => $searchTerm]);
    }



    #[Route('game/result/{guid}', name: 'game_show')]
    public function show(string $guid, GiantBombAPI $giantBombAPI, GameRepository $gameRepository, EntityManagerInterface $entityManager): Response
    {
        $game = $giantBombAPI->findGame($guid);
        return $this->render('game/show.html.twig', ['game' => $game]);
    }


    #[Route('/{id}/watchlist', methods: ['GET', 'POST'], name: 'game_watchlist')]

    public function addToWatchlist(Game $game, UserRepository $userRepository): Response

    {

        if (!$game) {

            throw $this->createNotFoundException(

                'No program with this id found in program\'s table.'

            );
        }


        /** @var \App\Entity\User */

        $user = $this->getUser();

        if ($user->isInFavorite($game)) {

            $user->removeGame($game);
        } else {

            $user->addGame($game);
        }


        $userRepository->save($user, true);

        return $this->json(['isInWatchlist' => $user->isInFavorite($game)]);
    }

    #[Route('game/personnage/show', name: 'character_show')]
    public function characterShow(Request $request, GiantBombAPI $giantBombAPI): Response
    {
        $guid = $request->query->get('id');
        $game = $giantBombAPI->findCharacter($guid);
        return $this->render('game/characterShow.html.twig', ['game' => $game, 'guid' => $guid]);
    }
}
