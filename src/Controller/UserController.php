<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    #[Route('/api/me')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function apiMe(): Response
    {
        return $this->json($this->getUser());
    }
}
