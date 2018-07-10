<?php

namespace App\Controller;

use App\Entity\District;
use App\Form\DistrictFilterForm;
use App\Form\DistrictForm;
use App\Service\District\DistrictService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/district")
 */
class DistrictController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var DistrictService
     */
    private $districtService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface      $flashBag
     * @param PaginatorInterface     $paginator
     * @param DistrictService        $districtService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        PaginatorInterface $paginator,
        DistrictService $districtService
    ) {
        $this->entityManager   = $entityManager;
        $this->flashBag        = $flashBag;
        $this->paginator       = $paginator;
        $this->districtService = $districtService;
    }

    /**
     * @Route("/", name="district.list")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function list(Request $request)
    {
        $page   = $request->query->getInt('page', 1);
        $params = $request->query->all();
        $form   = $this->createForm(DistrictFilterForm::class, $params);
        $query  = $this->entityManager
            ->getRepository('App:District')
            ->getPaginationQuery($params)
        ;

        $pagination = $this->paginator->paginate(
            $query,
            $page,
            10
        );

        return $this->render(
            'district/list.html.twig',
            [
                'pagination' => $pagination,
                'form'       => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/add/", name="district.add")
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function add(Request $request)
    {
        $district = new District();

        $form = $this->createForm(DistrictForm::class, $district);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            try {
                $this->entityManager->persist($district);
                $this->entityManager->flush();

                $this->flashBag->add('success', 'Dzielnica została dodana');
            } catch (\Exception $exception) {
                $this->flashBag->add('danger', 'Nie udało się dodać dzielnicy');
            }

            return $this->redirectToRoute('district.list');
        }

        return $this->render(
            'district/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/import/", name="district.import")
     *
     * @return JsonResponse
     */
    public function import()
    {
        $cities = $this->entityManager
            ->getRepository('App:City')
            ->findAll()
        ;

        if (true === empty($cities)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        foreach ($cities as $city) {
            $this->districtService->import($city);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/", name="district.show", requirements={"id"="\d+"})
     *
     * @param District $district
     *
     * @return Response
     */
    public function show(District $district)
    {
        return $this->render(
            'district/show.html.twig',
            [
                'district' => $district,
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", name="district.edit", requirements={"id"="\d+"})
     *
     * @param District $district
     * @param Request  $request
     *
     * @return RedirectResponse|Response
     */
    public function edit(District $district, Request $request)
    {
        $form = $this->createForm(DistrictForm::class, $district);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            try {
                $this->entityManager->flush();

                $this->flashBag->add('success', 'Dzielnica została zmodyfikowana');
            } catch (\Exception $exception) {
                $this->flashBag->add('danger', 'Nie udało się zapisać dzielnicy');
            }

            return $this->redirectToRoute('district.list');
        }

        return $this->render(
            'district/edit.html.twig',
            [
                'form'     => $form->createView(),
                'district' => $district,
            ]
        );
    }

    /**
     * @Route("/{id}/remove/", name="district.remove", requirements={"id"="\d+"})
     *
     * @param District $district
     *
     * @return RedirectResponse
     */
    public function remove(District $district)
    {
        try {
            $this->entityManager->remove($district);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Dzielnica została usunięta');
        } catch (\Exception $exception) {
            $this->flashBag->add('danger', 'Nie udało się usunąć dzielnicy - spróbuj ponownie później');
        }

        return $this->redirectToRoute('district.list');
    }
}
