<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 0],
            ],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = new JsonResponse();

        if ($exception instanceof HttpException) {
            $response->setData([
                'errors' => [
                    'title' => 'Not Found',
                    'status' => 404,
                    'detail' => 'The ressource requested doesn\'t exist',
                ]
            ]);
            $response->setStatusCode(404);
        }

        if ($exception instanceof NotEncodableValueException) {
            $response->setData([
                'errors' => [
                    'title' => 'Syntax Error',
                    'status' => 400,
                    'detail' => 'Json is not valid',
                ]
            ]);
            $response->setStatusCode(400);
        }

        if ($exception instanceof NotFoundHttpException) {
            $response->setData([
                'errors' => [
                    'title' => 'Not Found',
                    'status' => 404,
                    'detail' => 'The ressource requested doesn\'t exist',
                ]
            ]);
            $response->setStatusCode(404);
        }

        $event->setResponse($response);
    }
}
