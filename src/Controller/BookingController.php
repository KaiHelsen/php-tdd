<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Form\Type\BookingType;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use DateTime;
use SebastianBergmann\Comparator\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/')]
class BookingController extends AbstractController
{
    #[Route('/', name: 'rooms', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'controller_name' => 'BookingController',
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/booking/{room_id}', name: 'make_booking', methods: ['GET', 'POST'])]
    public function book(BookingRepository $bookingRepository, Room $room_id, Request $request, ValidatorInterface $validator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        //create new booking object an set default values
        $booking = new Booking();
        $booking->setUser($this->getUser());
        $booking->setRoom($room_id);
        $booking->setStartDate(new DateTime());
        $booking->setEndDate(new DateTime());

        // TODO: instead of findAll, just get the ones that have an end date AFTER now
        $booking->setBookingRecord($bookingRepository->findAfterDate(new DateTime('now'), $booking->getUser()));
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $booking = $form->getData();
            //store new booking in database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('rooms');
        }

        return $this->render('booking/newBooking.html.twig',
            ['form' => $form->createView(),
            ]);

    }
}
