<?php

namespace App\EventDispatcher;

use AddressInfo;
use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }
    public function sendEmail(ProductViewEvent $productViewEvent)
    {
        $email = new TemplatedEmail();
        $email->from(new Address("contact@mail.com", 'Infor de la boutique'))
            ->to("admin@mail.com")
            ->text("Un visiteur est sur la page produit n° " . $productViewEvent->getProduct()->getId())
            ->subject("Visite du produit " . $productViewEvent->getProduct()->getId())
            ->htmlTemplate("email/product_view.html.twig")
            ->context([
                'product' => $productViewEvent->getProduct()
            ]);
        //$this->mailer->send($email);

        $this->logger->info('produit vue ' . $productViewEvent->getProduct()->getId());
    }
}
