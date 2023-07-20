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

    #[Route('game/recherche', name: 'search_results', methods: ['GET'])]
    public function searchResults(Request $request, GiantBombAPI $giantBombAPI): Response
    {
        $searchTerm = $request->query->get('game_name','');

        $games = $giantBombAPI->searchGame($searchTerm);
        return $this->render('game/index.html.twig', ['games' => $games, 'searchTerm' => $searchTerm]);
    }



    #[Route('game/result/{guid}', name: 'game_show')]
    public function show(string $guid, GiantBombAPI $giantBombAPI): Response
    {
        $game = $giantBombAPI->findGame($guid);
        return $this->render('game/show.html.twig', ['game' => $game]);
    }
}
