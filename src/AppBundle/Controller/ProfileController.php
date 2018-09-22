<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;

class ProfileController extends Controller
{
    /**
     * @Route("/profile/{user}", name="profile")
     */
    public function indexAction(User $user)
    {
        $currentUser = $this->getUser();
        
        if ($user->getId() === $currentUser->getId()) {
            return $this->redirectToRoute('myProfile');
        }
        
        return $this->getProfilePage($user);
    }

    /**
     * @Route("/profile", name="myProfile")
     * @Method({"GET", "POST"})
     */
    public function getMyProfileAction() {
        $currentUser = $this->getUser();
        
        if ($currentUser) {
            return $this->getProfilePage($currentUser);
        }
        
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/profile/{user_id}/edit", name="editProfile")
     * @Security("(user && (user.getId() == user_id)) || has_role('ROLE_ADMIN')")
     */
    public function editAction($user_id = null)
    {
        $currentUser = $this->getUser();

        if ($user_id === null || $user_id == $currentUser->getId()) {
            $user = $currentUser;
        } else {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager
                ->getRepository(User::class)
                ->findOneBy([
                    'id' => $user_id
                ]);
        }

        if ($user === null) {
            throw $this->createNotFoundUserExpertion($user_id);
        }

        return $this->render('@App/Profile/index.html.twig', array(
            'user' => $user
        ));
    }
    
    protected function getProfilePage(User $user) {
        return $this->render('@App/Profile/index.html.twig', array(
                    'user' => $user
        ));
    }

    protected function createNotFoundUserExpertion($user_id) {
        return $this->createNotFoundException(sprintf('User with id %d was not found', $user_id));
    }
}
